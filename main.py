import os, json, datetime

with open("config.json", "r") as fd:
	config = json.load(fd)

now = datetime.date.today()
firstOfMonth = datetime.date(now.year, now.month, 1).weekday()

for entry in config:
	n = entry["nth-weekday"]
	day = entry["weekday"]
	if day >= firstOfMonth:
		n = n - 1

	target = (7 - firstOfMonth) + 7 * (n - 1) + day + 1

	reminder = target - entry["reminder"]
	if now.day == reminder:
		os.system(entry["command"])
