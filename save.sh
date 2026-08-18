#!/bin/bash

echo "Commit message:"
read commitMessage

if [ -z "$commitMessage" ]; then
    echo "Commit message cannot be empty."
    exit 1
fi

git add .
git commit -m "$commitMessage"

if [ $? -ne 0 ]; then
    echo "Commit failed. Push cancelled."
    exit 1
fi

git push

if [ $? -eq 0 ]; then
    echo "Changes successfully pushed to GitHub."
else
    echo "Push failed."
fi