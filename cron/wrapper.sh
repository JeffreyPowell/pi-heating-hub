#!/bin/bash

/usr/bin/python /home/pi/pi-heating-hub/cron/poll-sensors.py
/usr/bin/python /home/pi/pi-heating-hub/cron/update-timers.py
/usr/bin/python /home/pi/pi-heating-hub/cron/process-schedules.py
/usr/bin/python /home/pi/pi-heating-hub/cron/activate-devices.py
