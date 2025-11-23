<p align="center"><a href="https://github.com/martonpornoi" target="_blank"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRH0pBR5XcMZUmLkvykceRZ8JsPZwFW_EQZKkaci6q8Nx04m2e99-ccEAqjSbwxRlgaghw&usqp=CAU" width="400" alt="Laravel Logo"></a></p>

# Awoo TimeTracker

A simple tool for booking time spent on projects. This project is a homework for a volunteer position.

## Features

### Roles

- **User** can create time entries, and modify them until the project is Active.

- **Admin**, beyond User roles, can create projects and generate monthly reports. Admins can only be assigned through phpMyAdmin.

### Tools

- **Login System**: A simple add-on by Laravel, where users can add e-mail, password, and username. Since the task is not built around this module, integrating SMTP servers will not happen here.

- **Time Entries**: A form, where all users can assign date time, a project, time spent on a task, and can add description on of the task.

- **Projects**: Admins can create and modify projects by renaming and changing status of the project. It also lists all time entries.

- **Monthly Reports**: Admins can collect aggregated monthly reports. Per project, per user, as a total sum of overall time invested in all projects on a monthly basis. Reports can be deleted or re-generated.

## Install

### Windows

### OS X

### Ubuntu

## Database

Using MySQL through XAMPP. Latest schema is available on phpMyAdmin.

## Test

Love yourself. Sleep without stress.

### Unit Test

### Integration Test

### Smoke Test

## Contribution

Be not afraid to reach out with ideas. Even if you can't make it come true on your own, your enthusiasm will always be heard and prioritized.

### Pull Request

1) Create new branch.

2) Push your stuff on GitHub. Turn it into a Pull Request if you're feeling wild.

3) Make sure all workflow items pass.

4) Link your PR to an owner for review and wait for your fate.

5) Magic-magic (Ooh-Ooh). Magic-magic (Ooh-Ooh). Magic-magic-magic-magic~

### Review Guide

- Git commit message makes sense. Ticket is connected to task (not happening here).

- Code is easy to follow, or documentation can justify complexity.

- Proof that it works through tests and manual testing branch locally.

- Formatting and linting make the code seem like a nice picture on your wall.

- Documentation updated if necessary.

## Todo

- [x] Create landing page

- [x] Redirect registration page to time entries

- [x] List assigned time entries for projects

- [ ] Create unit tests

- [ ] Create integration tests

- [ ] Create smoke tests

- [ ] Automated workflows on GitHub

- [ ] Deployment rehearsal and documentation

- [ ] Last refactor scan
