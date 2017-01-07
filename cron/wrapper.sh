#!/bin/bash

python /home/pi/pi-heating-hub/cron/poll-sensors.py
python /home/pi/pi-heating-hub/cron/update-timers.py
python /home/pi/pi-heating-hub/cron/scan-network.py
python /home/pi/pi-heating-hub/cron/process-schedules.py
python /home/pi/pi-heating-hub/cron/activate-devices.py
