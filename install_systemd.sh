#!/bin/bash

# Copy systemd service files
cp systemd/*.service /etc/systemd/system

# Enable and start services
systemctl enable growapi
systemctl start growapi
systemctl enable growapi_test
systemctl start growapi_test

# Show status
systemctl --no-pager status growapi
systemctl --no-pager status growapi_test