#!/bin/bash
# Run this script to fix permissions on macOS with XAMPP

# Navigate to your project directory
cd /Applications/XAMPP/htdocs/rentacar

# Create upload directories if they don't exist
mkdir -p uploads/cars
mkdir -p uploads/profile_images

# Set proper permissions
chmod -R 755 uploads/
chown -R _www:_www uploads/

# Make sure Apache can write to these directories
chmod -R 777 uploads/

echo "Permissions fixed!"
