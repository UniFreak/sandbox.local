class Car():
    def __init__(self, make):
        self.make = make

        ode = 20
        self.ode = ode

c = Car(20)
print(c.make)

c.odmeter = 50
print(c.odmeter)