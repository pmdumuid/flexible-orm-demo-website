#!/bin/bash

#echo -n What is a dash-seperated URL name for the website?
DASH_SEPERATED_U_R_L_NAME=photo-sort
CAMEL_CASE_BASE_NAMESPACE=DumuidPhotoSort

sed "s=FlexibleORMDemoWebsite=$CAMEL_CASE_BASE_NAMESPACE=g" -i `git grep -l FlexibleORMDemoWebsite | grep -v ToYourWebsite.sh`
git commit -a -m "NEW WEBSITE - Update namespace from FlexibleORMDemoWebsite to $CAMEL_CASE_BASE_NAMESPACE"

git mv ./deployment/apache-conf/sites-available/flexible-orm-demo-website ./deployment/apache-conf/sites-available/$DASH_SEPERATED_U_R_L_NAME
sed "s=flexible-orm-demo-website=$DASH_SEPERATED_U_R_L_NAME=g" -i `git grep -l flexible-orm-demo-website | grep -v ToYourWebsite.sh`
git commit -a -m "NEW WEBSITE - Update website name from flexible-orm-demo-website to $DASH_SEPERATED_U_R_L_NAME"
