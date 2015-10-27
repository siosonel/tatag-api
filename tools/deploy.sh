#!/bin/bash

###########################################################################
# Purpose: 
# - Implements the build-locally, then deploy-to-remote approach
# - call from within project directory
# - example ~/.bashrc alias:   
#			alias deploy = [USER=username] 'tools/deploy.sh $1 $2 $3 $4' 
###########################################################################
 
set -e

# deploy expects $APP definition in tools/deploy_env.sh

# Detect arguments
if (($# == 1)); then
	AUDIENCE=$1
	ENV="stage"
	REV="head"
	OPT=""

elif (($# == 2)); then
	AUDIENCE=$1
	ENV=$2
	REV="head"
	OPT=""

elif (($# == 3)); then
	AUDIENCE=$1
	ENV=$2
	REV=$3	
	OPT=""

elif (($# == 4)); then
	AUDIENCE=$1
	ENV=$2
	REV=$3	
	OPT=$4	
	
else
	echo "Use: From project root, tools/deploy.sh APP_NAME AUDIENCE [ENVIRONMENT=stage] [REVISION_NUM=head] [OPT]"
	exit 1

fi

# deploy environments are declared and explained in $APP/tools/deploy_env.sh file
# note the dot at the start of the following line to keep vars in global context 
. ./tools/deploy_env.sh

echo "Processing build-deploy request of $APP $AUDIENCE, $REV, to $ENV."


# ************************************
#           VALIDATE INPUTS
# ************************************

# validate environment name
if [[ "$SERVER" == "" ]]; then
	echo "Unrecognized deploy environment '$AUDIENCE $ENV'. See $APP/tools/deploy_env.sh for valid environment names."
	exit 1
fi

# make the current branch head, that is being deployed, has been pushed to origin 
# if [[ "$ENV" == "live" || "$ENV" == "offline" || "$ENV" == "embargo" ]]; then
	# git push origin ${git rev-parse --abbrev-ref HEAD}
# fi




# *********************************
#              BUILD
# *********************************

# get full sha1 or convert 'head' to sha1
REV="$(git rev-parse --verify $REV)"
buildrev="$APP-$AUDIENCE-$REV"
BUILD="tools/builds/$buildrev"

if [[ ! -f $BUILD.tar.gz || "$OPT" == "-f" ]]; then 
	echo "building tar ... "
	tools/build.sh $USER $AUDIENCE $REV "$build_delete" $BUILD
fi



# ********************************
#            DEPLOY	 
# ********************************

tempdir="$REMOTE_DIR-$SVN_REV-z"

changeperm=""

# assume required deploy to stage before deploy to live
# and that different hosts uses the same home directory
if [[ "$ENV" == "stage" ]]; then
	echo "Sending tar ball to $SERVER"	
	scp -r $BUILD.tar.gz $USER@$SERVER:$xhome
fi

	
# as_user, like 'sudo -u apache' or empty string, is declared in $APP/tools/deploy_env.sh
ssh -t $USER@$SERVER "
	if [[ -d $REMOTE_DIR-X && ! -L $REMOTE_DIR-X ]] ; then
		$as_user rm -Rf $REMOTE_DIR-X
		$as_user rm -Rf $tempdir
	fi
	
	$as_user mkdir -p $tempdir
	$as_user tar -xf $xhome$buildrev.tar.gz -C $tempdir	
		
	if [[ -d $REMOTE_DIR && ! -L $REMOTE_DIR ]]; then
		$as_user mv -f $REMOTE_DIR $REMOTE_DIR-X
	fi
	
	$as_user chmod -R 755 $REMOTE_DIR/
	$as_user mv -f $tempdir $REMOTE_DIR	
	$as_user rm -Rf $REMOTE_DIR-X
	
	echo \"$(date) $REV $AUDIENCE deploy\" >> $REMOTE_DIR/rev.txt
"


# ******************************************
#            AUTOMATED TEST
# ******************************************

# call test URL (php-based tests)
# if test returns errors, rollback deployment?




# *****************************************
#            POST-DEPLOY
# *****************************************

# track
# targzrev=$(echo -n "value" | openssl sha1 -hmac "$(cat $BUILD.tar.gz)")
echo "$AUDIENCE $REV $(date) $targzrev" >> tools/builds/deploy_history.txt

if [[ $ENV != "temp" && $AUDIENCE != "dev" ]]; then
	git tag -f $AUDIENCE-$ENV
fi	

echo "Finished -- check for errors above, if any, for the build-deploy of $AUDIENCE $ENV ($REV)"

