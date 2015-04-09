#!/bin/bash

##############################################################
# Intended to be called as global vars within the shell context of
# tools/deploy.sh
# 
# Author: Edgar Sioson
# Date: 2015-03-16
#############################################################

APP="api"  # deploy to old directory name for now
if [[ "$USER" == "" ]]; then USER="root"; fi


case $AUDIENCE in
		public)		
				# build_delete="php/config-internal.php php/config-experts.php php/mortFileSha1.php README.txt"
				if [[ "$ENV" == "stage" ]]; then
					SERVER=tatag.cc
					REMOTE_DIR=/var/www/stage/$APP
					URL="http://stage.tatag.cc/$APP"
					xhome="~/builds/"				
				
        # for integration testing of new features, debugged code				
				elif [[ "$ENV" == "live" ]]; then
					SERVER=tatag.cc
					REMOTE_DIR=/var/www/html/api
					URL="http://tatag.cc/api/"
					xhome="~/builds/"
					
				fi
				;;
				
esac
