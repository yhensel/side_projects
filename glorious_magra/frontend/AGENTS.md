# Frontend Agent Instructions

## Architecture Principles

- Build a component-based UI with clear separation between presentation, state, and domain rules.
- Prefer a feature-first structure over a purely technical folder split when the app grows.
- Keep business logic out of UI components; use hooks, services, or state stores for side effects and shared behavior.
- Model important business concepts explicitly, but keep frontend DDD lightweight and practical.
- Keep routing and layout separate from page-specific view logic.

## Best Practices

- Use React with Vite and maintain a clean folder structure.
- Organize code by feature or bounded context when it improves clarity.
- Favor functional components and hooks.
- Use a state management strategy such as Redux Toolkit or Zustand for global application state.
- Keep forms and validation declarative using controlled inputs and validation utilities.
- Avoid direct DOM manipulation; use refs only when necessary.
- Keep styles scoped and modular; prefer CSS Modules, styled components, or utility-first CSS.
- Keep API access in dedicated services or data layers instead of spreading fetch logic through components.

## PWA and UX

- Ensure the app works as a Progressive Web App with an installable manifest and service worker.
- Optimize for mobile-first responsiveness and accessible interactions.
- Minimize bundle size and lazy-load routes or large components where appropriate.
- Use offline-friendly caching for static assets and app shell resources.

## Frontend Goals

- Implement authentication flows with JWT integration to the Symfony API.
- Build measurement forms and history views with strict client-side validation.
- Create group and social feed interfaces that consume backend endpoints.
- Use visualization components for progress charts and trend analysis.
- Test critical flows and ensure the app is installable and performant.
- Learn how to separate UI concerns from domain modeling without overengineering the frontend.
