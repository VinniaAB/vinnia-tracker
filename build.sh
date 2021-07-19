#!/bin/sh
echo "Copying files..."
rm -rf ./build
mkdir ./build
mkdir ./build/vinnia-tracker
rsync -av --exclude=".git/" --exclude=".github/" --exclude=".idea/" --exclude="vendor/" --exclude="build/" --exclude=".gitignore"  --exclude="build.sh" . build/vinnia-tracker > /dev/null

echo "Installing dependencies..."
composer install --no-dev --working-dir="./build/vinnia-tracker/" --quiet

echo "Creating archive..."
pushd ./build > /dev/null
zip -r -qq vinnia-tracker.zip vinnia-tracker
popd > /dev/null

echo "Cleaning up.."
rm -rf ./build/vinnia-tracker