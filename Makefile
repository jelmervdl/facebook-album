COFFEE = coffee
COFFEE_FLAGS =

.PHONEY: all

all: js/app.js

js/app.js: coffee/app.coffee coffee/linear_partition.coffee
	$(COFFEE) $(COFFEE_FLAGS) --compile --join $@ $^
