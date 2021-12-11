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
-[image:4038CA54-8D8A-4FA5-A8C0-9B384C716B11-10777-00000C66DBFFD59E/image.png]

The data model consists of:
* users (created by SSO signup if possible). A user can have a username, email, avatar, and personal bio. Potentially links to social media?
* a mixit group. This would allow us to use the same site to host different types of mixits, maybe for different discord servers. Even if the first version of the site only has one mixit group, I think it makes sense to build the data model and API to allow for that expansion.
* user capabilities. Each user is part of one or multiple mixit groups and can have different roles in each group. The simple roles would be admin and participant. An admin can basically do everything in a group: add users, remove users, create and delete mixits, remove other users samples, etc…
* mixits. This is the main object. Ideally, these can be created by anybody (one mixit per user), so that an admin doesn’t have to be around if people feel like hosting a mixit. Each mixit has a title, duration, start time, description, limits on sample size and count, a state. The state diagram of a mixit would be something similar to:
[image:86BFA5FA-81B2-413F-8D61-0C2837892820-10777-00000C6817E9E920/image.png]
We go from `CREATED` to `OPEN_FOR_SAMPLE_SUBMISSION` to `WAITING FOR START` to `RUNNING` (which allows submissions to be uploaded) to `FINISHED` (at which point submission is closed and commenting / liking can begin).
* samples: each mixit can have multiple samples by each user. A sample has a file, size, description, uploader, likes, comments.
* submissions: basically a big sample. Only one submission is allowed by a user.

## Pages
This is a list of pages and a very rough sketch of what I think they could look like

### Login page
[image:CEE8627C-616F-4505-9D30-ED85621C269D-10777-00000C68CE8C66AF/image.png]

### Mixit list / mixit group page
[image:262CA3CE-5A81-4946-9431-F33815A2562A-10777-00000C691351C5F9/image.png]

There would not be separate admin pages (except for user management), but additional admin widgets added to the normal pages (and potentially modals for certain actions).
### Single mixit page (not started)
[image:DAF83D1B-21F6-45DE-8D37-03996DBFE99E-10777-00000C699B70C5A6/image.png]

This would basically just show the mixit description, potentially some information about the planned duration and rules.
### Single mixit (sample submission)
[image:BE76D0A5-53C7-4412-ACDF-CEBCC41721BC-10777-00000C69E881D904/image.png]
 This is the page shown while sample upload is active. There is a big countdown in the middle, and a drag drop field (similar to soundcloud) to add samples. A modal then appears for a user to add a description of the sample while it is uploading.
### Single mixit (in progress)
[image:682C3918-5206-40CE-8AF6-AB70CB36CAB6-10777-00000C6A41D1BB13/image.png]
While the mixit is in progress, the individual samples can be downloaded, as well as zip of all the samples together. There is again a big countdown in the middle (which I forgot to draw), and an upload field for users to upload (or overwrite) their submission.
### Single mixit (finished)
[image:6C1EE137-CDB8-4E87-A39B-06639F7726D5-10777-00000C6ABE009CDA/image.png]
Once the mixit is finished, we just show a list of submission (potentially with player already, like in soundcloud). You can go to an individual submissions page by clicking on its title.
### Single submission
.[image:D9C21E69-71A7-4B68-A0DD-EB98B5CE1844-10777-00000C6AFFBF59CB/image.png]
This page shows the submission, along with description, comments.
### User page
[image:34D7182A-734A-4805-8306-8638839A77ED-10777-00000C6B456DF120/image.png]

The user page shows the avatar, bio, and sample/mixits uploaded by this user.
### User list page
To manage the users in the mixit group, we need an additional page. This also allows people to see who is active.
[image:AB8A25CC-8052-4AC6-8C4C-C93E79FF6867-10777-00000C6B975CC794/image.png]

## Implementation plan
A tentative implementation plan:
* Get a next.js app going
* Add the data model to supabase
* Make ugly-ass versions of each page
* Handle file upload and file storage
* Get beta users
* Prettify the layout / CSS
