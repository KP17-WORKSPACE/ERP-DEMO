# create-frog-mdp.py
# Generates frog-mdp.txt

N = 20  # chambers 1 to 20

num_states = N + 1  # states 0 to 20
num_actions = 2   
terminal_state = 0
discount = 1

# Regular jump 
p1, p2, p3 = 0.3, 0.6, 0.1

# Strong jump 
q1, q2, q3, q4 = 0.5, 0.2, 0.2, 0.1


def clip_state(s):
    if s < 0:
        return 0
    if s > N:
        return N
    return s


with open("frog-mdp.txt", "w") as f:

   
    f.write(f"numStates {num_states}\n")
    f.write(f"numActions {num_actions}\n")
    f.write(f"end {terminal_state}\n\n")

    
    for state in range(1, num_states):

        # Regular Jump
        transitions = {}

        next_states = [
            (state - 1, p1),
            (state,     p2),
            (state + 1, p3),
        ]

        for ns, prob in next_states:
            ns = clip_state(ns)
            transitions[ns] = transitions.get(ns, 0) + prob

        for ns, prob in transitions.items():
            reward = 0 if ns == terminal_state else -1
            f.write(f"transition {state} 0 {ns} {reward} {round(prob, 6)}\n")

        f.write("\n")

        # Strong Jump 
        transitions = {}

        next_states = [
            (state - 1, q1),
            (state,     q2),
            (state + 1, q3),
            (state + 2, q4),
        ]

        for ns, prob in next_states:
            ns = clip_state(ns)
            transitions[ns] = transitions.get(ns, 0) + prob

        for ns, prob in transitions.items():
            reward = 0 if ns == terminal_state else -1
            f.write(f"transition {state} 1 {ns} {reward} {round(prob, 6)}\n")

        f.write("\n")

    f.write(f"discount {discount}\n")
