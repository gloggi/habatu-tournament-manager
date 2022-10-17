
teams = list(range(1,9))

def roundRobin(teams):
    if not len(teams)%2:
        circleTeams = teams[:len(teams)-1]
        print(circleTeams)

roundRobin(teams)