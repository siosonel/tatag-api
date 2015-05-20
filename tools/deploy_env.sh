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
				build_append="ref/area_codes/*"
				
				if [[ "$ENV" == "stage" ]]; then
					SERVER=tatag.cc
					REMOTE_DIR=/var/www/stage/$APP
					URL="http://stage.tatag.cc/$APP"
					xhome="~/builds/"				
				
        # for integration testing of new features, debugged code				
				elif [[ "$ENV" == "live" ]]; then
					SERVER=tatag.cc
					REMOTE_DIR=/var/www/html/$APP
					URL="http://tatag.cc/$APP/"
					xhome="~/builds/"
					
				fi
				;;
		
		sim)
				# build_delete="php/config-internal.php php/config-experts.php php/mortFileSha1.php README.txt"
				build_append="ref/area_codes/*"
				
				if [[ "$ENV" == "stage" ]]; then
					SERVER=sim-stage.tatag.cc
					REMOTE_DIR=/var/www/sim-stage/$APP
					URL="http://sim-stage.tatag.cc/$APP"
					xhome="~/builds/"				
				
        # for integration testing of new features, debugged code				
				elif [[ "$ENV" == "live" ]]; then
					SERVER=sim-stage.tatag.cc
					REMOTE_DIR=/var/www/sim/$APP
					URL="http://sim.tatag.cc/$APP/"
					xhome="~/builds/"
					
				fi
				;;
		
esac
