#!/bin/bash
# Find and extract block styles from events.view.php and eventview.view.php
# For eventview.view.php: Check what block styles it has
grep -n "<style>" app/views/eventview.view.php || true
grep -n "</style>" app/views/eventview.view.php || true
