#!/bin/bash

sitename=$1;
codepath=$2;
environment=$3;
environmenturl=$4;
bbsource=$5;
owneremail=$6;
siteurl=$7;

curl --user dustinvannatter:euPdszcC8tewaNwv https://api.bitbucket.org/1.0/repositories/ --data name=$codepath --data is_private='true' --data owner=id-digital
git copy https://dustinvannatter:euPdszcC8tewaNwv@bitbucket.org/id-digital/$bbsource https://dustinvannatter:euPdszcC8tewaNwv@bitbucket.org/id-digital/$codepath

echo "code migrated..";
echo "";
