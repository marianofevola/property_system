Property System

This task is to test your knowledge of PHP, MySQL & Javascript, not of HTML or any
frameworks specifically. You are welcome to use any 3rd party libraries.

This is an open-book task - please do feel free to contact us with any questions or queries, and use the internet to find answers. Please note that the requirements are not exhaustive and you should address common concerns although not explicitly required.

Your Tasks

1) Create properties API endpoints (GET, DELETE, POST, PUT) for simple CRUD operations

a) properties should have: id (db generated), name (unique), price, type (flat, detached house, attached house) and creation moment

b) GET properties endpoint takes two arguments pageNo, perPage (write your own simple paginator)

c) Create a script meant to be run from CLI that generates from a defined dictionary properties to add using the above API; if the property exists update it. 
2) Property Admin Area

Create an admin area (no auth required) that allows linking real estate agents to properties. A property can have linked to multiple estate agents; when an agent is added to a property we also define the agents role in relation to the property (“viewing” and/or “selling”).

A real estate agent has the following fields: first name, last name (first name and last name together are unique), phone, email

The form should contain suitable validation.

3) Create an API endpoint that returns “top” agents. A top agent is one who has at least two properties in common with two other agents.

Example: 
Agent 1 (propr1, propr2, propr3), 
Agent 2 (propr2, propr3), 
Agent 3 (propr1, propr 3, propr5)
Agent 4 (propr3, propr4, propr6)
Agent 5 (propr1, propr2, propr5)
Agent 6 (propr4, propr6)

the endpoint should return Agent 1 (he has at least two properties in common with agent2 and agent3)  Agent 3, Agent 5
