# Sixty minute mixit revamp
## Overview
This document describes the reimplementation of the php sixtyminutemixit app. Sixtyminutemixit was a site that would allow you to host lightweight remix competition. We would use it on IRC to pass the time. The usual workflow was:
* for 10 minutes, participants could upload a couple of samples
* for 1h, participants would download the samples and make a full track
* the tracks would then get commented and liked on

This document brainstorms a revamp. I would personally like to use next.js (with typescript) and supabase for the backend. 

Next.js is a full-stack development solution that can easily be deployed to the cloud using the vercel saas offering. It is an opinionated way of building javascript websites with server side rendering easily mixed in. I personally don’t have much experience with next.js, a bit of experience with react, and used to be decently experienced at javascript in a previous life.

Supabase is a SaaS that offers authentication, storage and database (built on top of postgresql and postgrest). I personally am more comfortable with a relational database, and supabase is built on top of opensource components, so that it would be reasonably easy to port the stack over to a manually managed cloud deployment.

## Data model
![image](https://user-images.githubusercontent.com/128441/145682338-9f680781-4edb-4dd8-b1e8-2372a24dd2d0.png)

The data model consists of:
* users (created by SSO signup if possible). A user can have a username, email, avatar, and personal bio. Potentially links to social media?
* a mixit group. This would allow us to use the same site to host different types of mixits, maybe for different discord servers. Even if the first version of the site only has one mixit group, I think it makes sense to build the data model and API to allow for that expansion.
* user capabilities. Each user is part of one or multiple mixit groups and can have different roles in each group. The simple roles would be admin and participant. An admin can basically do everything in a group: add users, remove users, create and delete mixits, remove other users samples, etc…
* mixits. This is the main object. Ideally, these can be created by anybody (one mixit per user), so that an admin doesn’t have to be around if people feel like hosting a mixit. Each mixit has a title, duration, start time, description, limits on sample size and count, a state. The state diagram of a mixit would be something similar to:
![image](https://user-images.githubusercontent.com/128441/145682344-74f66576-142f-47f5-bd6d-cfe1c8d1ec95.png)

We go from `CREATED` to `OPEN_FOR_SAMPLE_SUBMISSION` to `WAITING FOR START` to `RUNNING` (which allows submissions to be uploaded) to `FINISHED` (at which point submission is closed and commenting / liking can begin).
* samples: each mixit can have multiple samples by each user. A sample has a file, size, description, uploader, likes, comments.
* submissions: basically a big sample. Only one submission is allowed by a user.

## Pages
This is a list of pages and a very rough sketch of what I think they could look like

### Login page
![image](https://user-images.githubusercontent.com/128441/145682353-11d2f6c0-f474-49a7-95d5-5607a0a92b23.png)

### Mixit list / mixit group page
![image](https://user-images.githubusercontent.com/128441/145682356-d22f838d-f6e9-4155-81bf-815d070eaac7.png)

There would not be separate admin pages (except for user management), but additional admin widgets added to the normal pages (and potentially modals for certain actions).

### Single mixit page (not started)

![image](https://user-images.githubusercontent.com/128441/145682359-65ab7177-f1c3-466a-930d-7d8de1aabe40.png)

This would basically just show the mixit description, potentially some information about the planned duration and rules.

### Single mixit (sample submission)

![image](https://user-images.githubusercontent.com/128441/145682362-01708c74-319d-4c98-914b-bffaac07ae2b.png)

This is the page shown while sample upload is active. There is a big countdown in the middle, and a drag drop field (similar to soundcloud) to add samples. A modal then appears for a user to add a description of the sample while it is uploading.

### Single mixit (in progress)

![image](https://user-images.githubusercontent.com/128441/145682373-c5325d7d-348f-40b8-b869-82e37843d3b2.png)

While the mixit is in progress, the individual samples can be downloaded, as well as zip of all the samples together. There is again a big countdown in the middle (which I forgot to draw), and an upload field for users to upload (or overwrite) their submission.

### Single mixit (finished)

![image](https://user-images.githubusercontent.com/128441/145682383-91e67083-b5b1-43ca-88ba-df014a0fa837.png)

Once the mixit is finished, we just show a list of submission (potentially with player already, like in soundcloud). You can go to an individual submissions page by clicking on its title.

### Single submission

![image](https://user-images.githubusercontent.com/128441/145682399-b1002795-c6f1-46e6-8ab1-44e6a99c1b20.png)

This page shows the submission, along with description, comments.

### User page

![image](https://user-images.githubusercontent.com/128441/145682403-e6698683-270f-421d-8005-477e8358012e.png)

The user page shows the avatar, bio, and sample/mixits uploaded by this user.

### User list page

To manage the users in the mixit group, we need an additional page. This also allows people to see who is active.

![image](https://user-images.githubusercontent.com/128441/145682407-b2cefda5-612a-4e3d-ae25-e737e41a5d96.png)

## Implementation plan
A tentative implementation plan:
* Get a next.js app going
* Add the data model to supabase
* Make ugly-ass versions of each page
* Handle file upload and file storage
* Get beta users
* Prettify the layout / CSS
