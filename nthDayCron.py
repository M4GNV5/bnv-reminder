import os, json, datetime, calendar

with open("config.json", "r") as fd:
	config = json.load(fd)

now = datetime.date.today()
firstWeekday, monthDays = calendar.monthrange(now.year, now.month)

for entry in config:
	n = entry["nth-weekday"]
	day = entry["weekday"]

	if n > 0:
		if day >= firstWeekday:
			n = n - 1

		target = (7 - firstWeekday) + 7 * (n - 1) + day + 1
	else:
		n = -n
		lastWeekday = calendar.weekday(now.year, now.month, monthDays)
		if day <= lastWeekday:
			n = n - 1

		target = monthDays - (lastWeekday + 7 * n - day)

	reminder = now.replace(day=target) - datetime.timedelta(entry["reminder"])
	print(now.replace(day=target), reminder)
	if now.day == reminder:
		os.system(entry["command"])
