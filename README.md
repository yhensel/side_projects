# Side Projects

This repository contains personal side projects and experiments by Yhensel.

## Projects

### [`glorious_magra`](glorious_magra/)

Glorious Magra is a full-stack, solo-first fitness progress platform for recording body measurements, calculating body-fat percentage with the U.S. Navy method, tracking goals, and reviewing progress history. Optional groups and activity feeds add a social layer without blocking the individual experience.

It is built as a React progressive web app connected to a Symfony API Platform backend. The backend uses JWT authentication, Doctrine ORM, and a domain-driven architecture, with Docker providing the local development environment.

### Website (repository root)

The repository root contains Yhensel Benitez's bilingual personal CV and portfolio website. It presents professional experience, education, technical skills, and engineering practices in English and Spanish, with responsive layouts and locally bundled assets.

The site is a static HTML, CSS, and vanilla JavaScript project using Bootstrap and frontend libraries such as AOS, Typed.js, GLightbox, Isotope, Swiper, and PureCounter. Its entry point is `index.html`; styles, scripts, images, and vendor dependencies are organized under `assets/`. It can be opened directly in a browser or served with a simple local HTTP server:

```bash
python3 -m http.server 8000
```

Then visit <http://localhost:8000>.

See [Glorious Magra's README](glorious_magra/README.md) for the full-stack project's setup and development instructions.

## Repository Layout

```text
side_projects/
├── index.html
├── assets/              Static website styles, scripts, media, and vendors
├── glorious_magra/
└── README.md
```

## Git Remote

The canonical remote repository is:

```text
git@github.com:yhensel/side_projects.git
```