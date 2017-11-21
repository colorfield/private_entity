Simple per entity access flag limited to the view operation.

## Configuration

- Create a private field for the entity type / bundle you want to limit access.
- Set the permissions for the roles that can view the private entities.
- Make some entities private

Currently, 

The first version will be limited to the Node entity type.

## Use case

Suitable when you want

- A single privacy rule for viewing content that can be applied per role.
- A private flag accessible straight from the entity create and edit form.
- To filter private content easily with Views.

## Roadmap

- Redirect 403 to user login then redirect after login to the entity 
that tried to be accessed.
- Use a custom publishing option as an alternative to a field.
- Bulk edit from the /admin/content list.
