# Side Projects

This repository contains personal side projects and experiments by Yhensel.

## Projects

### [`glorious_magra`](glorious_magra/)

Glorious Magra is a full-stack, solo-first fitness progress platform for recording body measurements, calculating body-fat percentage with the U.S. Navy method, tracking goals, and reviewing progress history. Optional groups and activity feeds add a social layer without blocking the individual experience.

It is built as a React progressive web app connected to a Symfony API Platform backend. The backend uses JWT authentication, Doctrine ORM, and a domain-driven architecture, with Docker providing the local development environment.

### [`website`](website/)

This is Yhensel Benitez's bilingual personal CV and portfolio website. It presents professional experience, education, technical skills, and engineering practices in English and Spanish, with responsive layouts and locally bundled assets.

The site is a static HTML, CSS, and vanilla JavaScript project using Bootstrap and frontend libraries such as AOS, Typed.js, GLightbox, Isotope, Swiper, and PureCounter. It can be opened directly in a browser or served with a simple local HTTP server.

Each project contains its own files and documentation. See the relevant project directory for setup and development instructions.

## Repository Layout

```text
side_projects/
├── glorious_magra/
└── website/
```

## Git Remote

The canonical remote repository is:

```text
git@github.com:yhensel/side_projects.git
```