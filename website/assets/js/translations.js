const translations = {
  en: {
    // Meta
    "meta.title": "Yhensel Benítez — Senior Backend Engineer",
    "meta.description": "Senior Backend Engineer with 10+ years of experience in PHP, Symfony, AWS, Kafka, Docker, Kubernetes, MySQL, MongoDB, microservices, and distributed systems. Based in Barcelona, open to remote.",

    // Nav
    "nav.home": "Home",
    "nav.about": " About",
    "nav.resume": " Resume",

    // Hero
    "hero.prefix": "I'm a ",
    "hero.typed": "Backend Architect, Distributed Systems Engineer, DDD Practitioner",
    "hero.role": "Senior Backend Engineer",

    // About
    "about.title": "About",
    "about.intro": 'Senior Backend Engineer with <b>10+ years</b> of experience designing and shipping scalable distributed systems in <b>PHP/Symfony</b>. Specialized in microservices architecture, DDD, and event-driven systems using <b>Kafka, RabbitMQ, AWS, and Kubernetes</b>. Proven track record driving monolith-to-microservices migrations, building real-time data pipelines, and deploying observability infrastructure — consistently improving system reliability, team velocity, and business outcomes.',
    "about.subtitle": "Senior Backend Engineer",
    "about.personal": "Outside of work, I enjoy exploring new technologies through side projects, staying active, and spending time with my dog. I'm driven by curiosity and energized by complex, meaningful problems.",
    "about.location.label": "Location:",
    "about.location.value": "Barcelona, Spain",
    "about.workmode.label": "Work mode:",
    "about.workmode.value": "Remote / Hybrid",
    "about.education.label": "Education:",
    "about.education.value": "B.Sc. Computer Science (in progress)",
    "about.languages.label": "Languages:",
    "about.languages.value": "Spanish (native), English (professional)",
    "about.closing": "A key contributor in every team I've been part of — driving monolith-to-microservices migrations, championing best practices, and partnering with product owners on technical strategy. I prioritize testability, maintainability, and developer experience.",

    // Resume
    "resume.title": "Experience & Education",
    "resume.subtitle": "A snapshot of my career — the teams I've been part of, the systems I've helped build, and the impact I've delivered.",
    "resume.education": "Education",
    "resume.experience": "Professional Experience",

    // Education items
    "edu.uoc.title": "B.Sc. in Computer Science",
    "edu.uoc.date": "2025 – Present",
    "edu.uoc.desc": "After a decade of hands-on engineering, I'm completing my CS degree to formalize the theoretical foundations — algorithms, distributed systems theory, and software engineering — that complement my production experience.",
    "edu.daw.title": "Higher Ed. Certificate in Web Application Development (EQF Level 5)",
    "edu.daw.date": "2013 – 2014",
    "edu.asix.title": "Higher Ed. Certificate in Network Systems Administration (EQF Level 5)",
    "edu.asix.date": "2010 – 2012",

    // Stay
    "exp.stay.title": "Senior Backend Engineer",
    "exp.stay.date": "Dec 2023 – Present",
    "exp.stay.company": "Stay — Madrid, Spain (Remote from Barcelona)",
    "exp.stay.desc": "Part of the backend team building the hospitality platform powering hotel integrations, real-time analytics, and guest-facing features across multiple hotel chains.",
    "exp.stay.bullet1": "Architect DDD-based backend services (PHP/Symfony) with clean bounded contexts, enabling 3 independent squads to ship features in parallel and reducing cross-team dependencies by ~40%.",
    "exp.stay.bullet2": "Integrate multiple hotel chain REST APIs for the 360 project — unlocking fast-booking, loyalty programs, and Wallet features serving thousands of daily transactions.",
    "exp.stay.bullet3": "Build real-time analytics pipelines with Kafka and Tinybird, processing 500K+ daily events through optimized schemas and dimension tables.",
    "exp.stay.bullet4": "Deploy and maintain full observability stack (Grafana, Loki, OpenSearch) — reducing mean incident response time from hours to under 15 minutes.",
    "exp.stay.bullet5": "Drive refactoring of legacy Doctrine ORM mappings, standardizing REST API responses and eliminating data inconsistencies across 10+ microservices.",

    // GoTrendier
    "exp.gotrendier.title": "Senior Backend Engineer",
    "exp.gotrendier.date": "Mar 2023 – Dec 2023",
    "exp.gotrendier.company": "GoTrendier — Barcelona, Spain",
    "exp.gotrendier.desc": 'Powered search and product features for a second-hand fashion marketplace serving <b>7M+ products</b> and <b>~2M users</b> across Mexico and Colombia, in a lean 3-person engineering team.',
    "exp.gotrendier.bullet1": "Owned the Elasticsearch-powered search system end-to-end — improving search relevance by 30% and reducing p95 query latency to <200ms across 7M+ listings.",
    "exp.gotrendier.bullet2": "Built a product validation and moderation tool that improved content quality and platform trust.",
    "exp.gotrendier.bullet3": "Shipped features autonomously from requirements analysis through production deployment, collaborating directly with product and design.",

    // Zinio
    "exp.zinio.title": "Backend Engineer (Mid → Senior)",
    "exp.zinio.date": "2018 – 2023",
    "exp.zinio.company": "Zinio — Barcelona, Spain",
    "exp.zinio.desc": "Core member of a 3–5 person team building the content ingestion pipeline for a B2C digital magazine platform, serving major publishers including Meredith (People, InStyle, Food&Wine).",
    "exp.zinio.bullet1": "Drove the migration from a legacy PHP monolith to a microservices architecture (Symfony, Docker, AWS), improving deployment frequency from monthly to weekly releases.",
    "exp.zinio.bullet2": "Designed DDD-based backend services powering a high-volume content pipeline processing 10K+ magazine issues/month for publishers like Meredith (People, InStyle).",
    "exp.zinio.bullet3": "Implemented event-driven messaging with RabbitMQ, decoupling 8+ services and achieving 99.9% message delivery reliability under peak loads.",
    "exp.zinio.bullet4": "Built observability infrastructure (Kibana, CloudWatch, Sentry) with custom metrics — enabling proactive incident detection and faster resolution.",
    "exp.zinio.bullet5": "Shipped features end-to-end and partnered with the Product Owner on technical proposals and estimation.",

    // Atabix
    "exp.atabix.title": "Junior Developer",
    "exp.atabix.date": "2017 – 2018",
    "exp.atabix.company": "Atabix — Barcelona, Spain",
    "exp.atabix.desc": "Contributed to a full redesign of an internal management tool for 2theloo (washroom management in large commercial spaces). Built features for store collections, employee management, and company billing in a 3-person team.",

    // AT Sistemas
    "exp.at.title": "Junior Developer",
    "exp.at.date": "2016 – 2017",
    "exp.at.company": "AT Sistemas — Jerez de la Frontera, Spain",
    "exp.at.desc": "Built a bridge application to automate hotel reservation and rate transfers between systems — converting SOAP payloads to a JSON API, with RabbitMQ for queuing and Memcache for caching. Included a 3-month on-site rotation abroad.",

    // Skills
    "skills.title": "Tech Stack",
    "skills.subtitle": "The tools and technologies I use daily to build reliable, scalable systems.",
    "skills.languages": "Languages",
    "skills.frameworks": "Frameworks & Libraries",
    "skills.databases": "Databases & Search",
    "skills.cloud": "Cloud & Infrastructure",
    "skills.messaging": "Messaging & Streaming",
    "skills.observability": "Observability",
    "skills.arch": "Architecture & Practices",
    "skills.tools": "Tools & Version Control",

    // How I Work
    "soft.title": "How I Work",
    "soft.subtitle": "Strong engineering is a team sport. These are the principles that shape how I collaborate.",
    "soft.ownership.label": "Ownership & Initiative:",
    "soft.ownership.desc": " I don't wait for tickets — I spot problems, propose solutions, and drive them forward.",
    "soft.communication.label": "Clear Communication:",
    "soft.communication.desc": " I bridge the gap between technical complexity and business context, making sure stakeholders and engineers stay aligned.",
    "soft.feedback.label": "Constructive Feedback:",
    "soft.feedback.desc": " I give thorough code reviews and welcome honest critique — it's how teams grow.",
    "soft.tradeoffs.label": "Pragmatic Trade-offs:",
    "soft.tradeoffs.desc": " I balance ideal architecture with shipping deadlines, finding solutions that are both clean and deliverable.",

    // AI
    "ai.title": "AI-Enhanced Development",
    "ai.subtitle": "I leverage AI tools daily — from code reviews to architecture exploration — multiplying output without compromising quality, so I can focus on high-impact decisions.",
    "ai.pairing.title": "AI-Powered Pairing",
    "ai.pairing.desc": "Using LLMs as a thinking partner for design, refactoring, and problem-solving.",
    "ai.review.title": "Automated Code Review",
    "ai.review.desc": "Catching edge cases, improving readability, and enforcing standards faster.",
    "ai.perf.title": "Performance Optimization",
    "ai.perf.desc": "Profiling queries, identifying bottlenecks, and exploring optimization strategies.",

    // Footer
    "footer.copyright": "Copyright",
    "footer.rights": "All Rights Reserved"
  },

  es: {
    // Meta
    "meta.title": "Yhensel Benítez — Senior Backend Engineer",
    "meta.description": "Senior Backend Engineer con más de 10 años de experiencia en PHP, Symfony, AWS, Kafka, Docker, Kubernetes, MySQL, MongoDB, microservicios y sistemas distribuidos. Con base en Barcelona, disponible en remoto.",

    // Nav
    "nav.home": "Inicio",
    "nav.about": " Sobre mí",
    "nav.resume": " Currículum",

    // Hero
    "hero.prefix": "Soy ",
    "hero.typed": "Arquitecto Backend, Ingeniero de Sistemas Distribuidos, Practicante de DDD",
    "hero.role": "Senior Backend Engineer",

    // About
    "about.title": "Sobre mí",
    "about.intro": 'Senior Backend Engineer con <b>más de 10 años</b> de experiencia diseñando y entregando sistemas distribuidos escalables en <b>PHP/Symfony</b>. Especializado en arquitectura de microservicios, DDD y sistemas event-driven con <b>Kafka, RabbitMQ, AWS y Kubernetes</b>. Trayectoria demostrada impulsando migraciones de monolitos a microservicios, construyendo pipelines de datos en tiempo real y desplegando infraestructura de observabilidad — mejorando consistentemente la fiabilidad del sistema, la velocidad del equipo y los resultados de negocio.',
    "about.subtitle": "Senior Backend Engineer",
    "about.personal": "Fuera del trabajo, disfruto explorando nuevas tecnologías con proyectos personales, manteniéndome activo y pasando tiempo con mi perro. Me mueve la curiosidad y me motivan los problemas complejos con impacto real.",
    "about.location.label": "Ubicación:",
    "about.location.value": "Barcelona, España",
    "about.workmode.label": "Modalidad:",
    "about.workmode.value": "Remoto / Híbrido",
    "about.education.label": "Educación:",
    "about.education.value": "Grado en Ingeniería Informática (en curso)",
    "about.languages.label": "Idiomas:",
    "about.languages.value": "Español (nativo), Inglés (profesional)",
    "about.closing": "Pieza clave en cada equipo del que he formado parte — impulsando migraciones de monolitos a microservicios, promoviendo buenas prácticas y colaborando con product owners en estrategia técnica. Priorizo la testabilidad, mantenibilidad y la experiencia del desarrollador.",

    // Resume
    "resume.title": "Experiencia y Educación",
    "resume.subtitle": "Un resumen de mi carrera — los equipos de los que he formado parte, los sistemas que he ayudado a construir y el impacto que he generado.",
    "resume.education": "Educación",
    "resume.experience": "Experiencia Profesional",

    // Education items
    "edu.uoc.title": "Grado en Ingeniería Informática",
    "edu.uoc.date": "2025 – Actualidad",
    "edu.uoc.desc": "Tras una década de experiencia práctica en ingeniería, estoy completando mi grado en Informática para formalizar los fundamentos teóricos — algoritmos, teoría de sistemas distribuidos e ingeniería de software — que complementan mi experiencia en producción.",
    "edu.daw.title": "Grado Superior en Desarrollo de Aplicaciones Web (DAW)",
    "edu.daw.date": "2013 – 2014",
    "edu.asix.title": "Grado Superior en Administración de Sistemas en Red (ASIX)",
    "edu.asix.date": "2010 – 2012",

    // Stay
    "exp.stay.title": "Senior Backend Engineer",
    "exp.stay.date": "Dic 2023 – Actualidad",
    "exp.stay.company": "Stay — Madrid, España (Remoto desde Barcelona)",
    "exp.stay.desc": "Parte del equipo backend construyendo la plataforma de hospitalidad que integra hoteles, analítica en tiempo real y funcionalidades orientadas al huésped en múltiples cadenas hoteleras.",
    "exp.stay.bullet1": "Arquitecto servicios backend basados en DDD (PHP/Symfony) con bounded contexts bien definidos, permitiendo a 3 squads independientes entregar funcionalidades en paralelo y reduciendo dependencias entre equipos en ~40%.",
    "exp.stay.bullet2": "Integro múltiples APIs REST de cadenas hoteleras para el proyecto 360 — habilitando fast-booking, programas de fidelización y funcionalidades de Wallet sirviendo miles de transacciones diarias.",
    "exp.stay.bullet3": "Construyo pipelines de analítica en tiempo real con Kafka y Tinybird, procesando más de 500K eventos diarios mediante esquemas optimizados y tablas de dimensiones.",
    "exp.stay.bullet4": "Despliego y mantengo stack completo de observabilidad (Grafana, Loki, OpenSearch) — reduciendo el tiempo medio de respuesta ante incidentes de horas a menos de 15 minutos.",
    "exp.stay.bullet5": "Impulso la refactorización de mappings legacy de Doctrine ORM, estandarizando respuestas de API REST y eliminando inconsistencias de datos en más de 10 microservicios.",

    // GoTrendier
    "exp.gotrendier.title": "Senior Backend Engineer",
    "exp.gotrendier.date": "Mar 2023 – Dic 2023",
    "exp.gotrendier.company": "GoTrendier — Barcelona, España",
    "exp.gotrendier.desc": 'Impulsé las funcionalidades de búsqueda y producto para un marketplace de moda de segunda mano con <b>más de 7M de productos</b> y <b>~2M de usuarios</b> en México y Colombia, en un equipo lean de 3 ingenieros.',
    "exp.gotrendier.bullet1": "Responsable end-to-end del sistema de búsqueda con Elasticsearch — mejorando la relevancia de búsqueda en un 30% y reduciendo la latencia p95 a <200ms en más de 7M de listados.",
    "exp.gotrendier.bullet2": "Construí una herramienta de validación y moderación de productos que mejoró la calidad del contenido y la confianza de la plataforma.",
    "exp.gotrendier.bullet3": "Entregué funcionalidades de forma autónoma, desde el análisis de requisitos hasta el despliegue en producción, colaborando directamente con producto y diseño.",

    // Zinio
    "exp.zinio.title": "Backend Engineer (Mid → Senior)",
    "exp.zinio.date": "2018 – 2023",
    "exp.zinio.company": "Zinio — Barcelona, España",
    "exp.zinio.desc": "Miembro clave de un equipo de 3–5 personas construyendo el pipeline de ingesta de contenido para una plataforma B2C de revistas digitales, sirviendo a grandes editores como Meredith (People, InStyle, Food&Wine).",
    "exp.zinio.bullet1": "Impulsé la migración de un monolito PHP legacy a una arquitectura de microservicios (Symfony, Docker, AWS), mejorando la frecuencia de despliegue de mensual a semanal.",
    "exp.zinio.bullet2": "Diseñé servicios backend basados en DDD para un pipeline de contenido de alto volumen, procesando más de 10K ejemplares de revistas/mes para editores como Meredith (People, InStyle).",
    "exp.zinio.bullet3": "Implementé mensajería event-driven con RabbitMQ, desacoplando más de 8 servicios y alcanzando un 99.9% de fiabilidad en la entrega de mensajes bajo cargas pico.",
    "exp.zinio.bullet4": "Construí infraestructura de observabilidad (Kibana, CloudWatch, Sentry) con métricas personalizadas — permitiendo la detección proactiva de incidentes y una resolución más rápida.",
    "exp.zinio.bullet5": "Entregué funcionalidades end-to-end y colaboré con el Product Owner en propuestas técnicas y estimaciones.",

    // Atabix
    "exp.atabix.title": "Desarrollador Junior",
    "exp.atabix.date": "2017 – 2018",
    "exp.atabix.company": "Atabix — Barcelona, España",
    "exp.atabix.desc": "Contribuí al rediseño completo de una herramienta de gestión interna para 2theloo (gestión de aseos en grandes superficies comerciales). Desarrollé funcionalidades para la gestión de tiendas, empleados y facturación en un equipo de 3 personas.",

    // AT Sistemas
    "exp.at.title": "Desarrollador Junior",
    "exp.at.date": "2016 – 2017",
    "exp.at.company": "AT Sistemas — Jerez de la Frontera, España",
    "exp.at.desc": "Construí una aplicación puente para automatizar la transferencia de reservas y tarifas hoteleras entre sistemas — convirtiendo payloads SOAP a una API JSON, con RabbitMQ para colas y Memcache para caché. Incluyó una rotación de 3 meses en el extranjero.",

    // Skills
    "skills.title": "Stack Tecnológico",
    "skills.subtitle": "Las herramientas y tecnologías que uso a diario para construir sistemas fiables y escalables.",
    "skills.languages": "Lenguajes",
    "skills.frameworks": "Frameworks y Librerías",
    "skills.databases": "Bases de Datos y Búsqueda",
    "skills.cloud": "Cloud e Infraestructura",
    "skills.messaging": "Mensajería y Streaming",
    "skills.observability": "Observabilidad",
    "skills.arch": "Arquitectura y Prácticas",
    "skills.tools": "Herramientas y Control de Versiones",

    // How I Work
    "soft.title": "Cómo Trabajo",
    "soft.subtitle": "La buena ingeniería es un deporte de equipo. Estos son los principios que definen cómo colaboro.",
    "soft.ownership.label": "Proactividad e Iniciativa:",
    "soft.ownership.desc": " No espero a que lleguen los tickets — detecto problemas, propongo soluciones y las impulso.",
    "soft.communication.label": "Comunicación Clara:",
    "soft.communication.desc": " Conecto la complejidad técnica con el contexto de negocio, asegurando que stakeholders e ingenieros estén alineados.",
    "soft.feedback.label": "Feedback Constructivo:",
    "soft.feedback.desc": " Hago code reviews exhaustivas y agradezco las críticas honestas — así es como crecen los equipos.",
    "soft.tradeoffs.label": "Trade-offs Pragmáticos:",
    "soft.tradeoffs.desc": " Equilibro la arquitectura ideal con los plazos de entrega, encontrando soluciones que sean limpias y viables.",

    // AI
    "ai.title": "Desarrollo con IA",
    "ai.subtitle": "Uso herramientas de IA a diario — desde code reviews hasta exploración de arquitectura — multiplicando mi output sin comprometer la calidad, para centrarme en decisiones de alto impacto.",
    "ai.pairing.title": "Pair Programming con IA",
    "ai.pairing.desc": "Uso LLMs como compañero de pensamiento para diseño, refactorización y resolución de problemas.",
    "ai.review.title": "Code Review Automatizada",
    "ai.review.desc": "Detectando edge cases, mejorando legibilidad y aplicando estándares más rápido.",
    "ai.perf.title": "Optimización de Rendimiento",
    "ai.perf.desc": "Análisis de queries, identificación de cuellos de botella y exploración de estrategias de optimización.",

    // Footer
    "footer.copyright": "Copyright",
    "footer.rights": "Todos los Derechos Reservados"
  }
};

