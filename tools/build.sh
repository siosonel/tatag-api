#!/bin/bash

#############################################################################
#
# Has to be called from the project directory that is tracked by git
#
#############################################################################

USER=$1
AUDIENCE=$2
REV=$3
DELETES=$4
BUILDNAME=$5

if [[ "$USER" == "" || "$AUDIENCE" == "" || "$REV" == "" ]]; then
	echo "Usage: from the project root, path/to/build.sh USER AUDIENCE ENV REV"
	exit 1
fi


# create a unique clean directory to do the rest of the work
# this will prevent the tar extraction from affecting project files
mkdir -p tools/prep-$REV

echo "Building the tar ball:"

# git-archive must be called from within a git tracked directory
# see above note about buildep.sh being called from project root	
git archive -o tools/prep-$REV/build.tar $REV

cd tools/prep-$REV

# add configuration file
cp ../../config-$AUDIENCE.php .

# will attach a revision info file to permit quick inspection on deployment site
echo "$(date) $REV" > rev.txt

# append files to build
tar --append --file=build.tar rev.txt config-$AUDIENCE.php --exclude-vcs

# delete files as necessary
echo "Deleting from built tar: $build_delete"
tar --delete --file=build.tar tools test $DELETES

gzip -vf build.tar

cd ../..

# move the procesed tar file to builds directory
if [[ ! -d tools/builds ]]; then mkdir -p tools/builds; fi
mv tools/prep-$REV/build.tar.gz "$BUILDNAME.tar.gz"

rm -R tools/prep-$REV 

