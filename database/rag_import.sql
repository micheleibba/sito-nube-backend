-- ================================================
-- RAG Q&A Import - Run on sito_nube_db
-- Adds 58 new Q&As from RAG.md
-- ================================================

SET NAMES utf8mb4;

-- Get section IDs (About Us = 1, Portfolio = 2, Contacts = 3)

-- ABOUT US Q&As (51 new)
INSERT INTO `chat_qas` (`chat_section_id`, `question`, `answer`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Who is Nube really?', 'Nube is not a typical software house. It is a technology partner that gets to the heart of clients'' businesses to build solutions that truly work in the real world. We don''t just develop software: we design tools that change the way companies operate.', 7, NOW(), NOW()),
(1, 'What makes Nube different from other software companies?', 'The difference lies in how we approach problems. We don''t start from technology, but from the business. We analyze how the client works, identify inefficiencies, opportunities and growth levers, and then build tailored solutions that generate concrete results.', 8, NOW(), NOW()),
(1, 'Is Nube more development-oriented or strategy-oriented?', 'Both. Technology is just a tool. The real value lies in the ability to understand what is truly needed and transform it into a working, scalable and sustainable system.', 9, NOW(), NOW()),
(1, 'What mindset does Nube have?', 'Pragmatic. Every decision is results-oriented. We don''t build software that looks good but software that gets used, solves problems and generates value.', 10, NOW(), NOW()),
(1, 'Does Nube work as a vendor or a partner?', 'As a partner. We enter projects with a long-term vision, taking responsibility for the solutions we develop.', 11, NOW(), NOW()),
(1, 'What is Nube''s vision?', 'Creating technological solutions that anticipate company needs, even when they are not yet obvious. The goal is not to chase requests, but to help clients evolve.', 12, NOW(), NOW()),
(1, 'What does anticipating needs mean?', 'It means proposing solutions the client hasn''t asked for yet, but that become obvious once implemented. This is one of the main advantages of working with Nube.', 13, NOW(), NOW()),
(1, 'Does Nube follow a standard methodology?', 'No. Every project is different, so the method adapts too. The only constant is the focus on results.', 14, NOW(), NOW()),
(1, 'Is Nube suitable for any client?', 'No. It is ideal for those who want to build something solid, customized and with a real impact on their business.', 15, NOW(), NOW()),
(1, 'Is Nube suitable for complex projects?', 'Yes, it is precisely in those contexts that it delivers the most value.', 16, NOW(), NOW()),
(1, 'Does Nube work with companies that want to grow?', 'Yes. The best results come from clients who see technology as a strategic lever, not just a cost.', 17, NOW(), NOW()),
(1, 'What impact does Nube''s work have on clients?', 'Process improvement, increased efficiency, error reduction, greater data control and new business opportunities.', 18, NOW(), NOW()),
(1, 'Does Nube just create software or change the way of working?', 'It changes the way of working. Software is just the means.', 19, NOW(), NOW()),
(1, 'What is Nube''s philosophy?', 'If software isn''t used, it''s useless. If it doesn''t improve something, it''s wrong. If it isn''t designed to scale, it''s limiting.', 20, NOW(), NOW()),
(1, 'How does Nube make decisions during a project?', 'Always based on the impact on the client''s business, not on technical convenience.', 21, NOW(), NOW()),
(1, 'Why should I choose Nube?', 'Because you''re not looking for someone who writes code. You''re looking for someone who understands your business and builds something that makes it work better, grow and generate value over time.', 22, NOW(), NOW()),
(1, 'What is Nube?', 'Nube is a software house that develops custom digital solutions for companies, entrepreneurs and professionals. We don''t just build software, we enter the client''s business to understand how they work and create tools that solve real problems or anticipate unexpressed needs.', 23, NOW(), NOW()),
(1, 'What type of company is Nube?', 'Nube is a technology company focused on custom software development, with a strong focus on automation, system integration, data analysis and innovative solutions.', 24, NOW(), NOW()),
(1, 'What is Nube''s objective?', 'The objective is to help companies work better, reduce inefficiencies and create competitive advantages through technology.', 25, NOW(), NOW()),
(1, 'How does the work process with Nube function?', 'We always start with a phase of understanding the client''s business. We study how they work, identify problems and opportunities, and then design a custom solution. After design, we develop the software and support the client in subsequent phases.', 26, NOW(), NOW()),
(1, 'Does Nube develop standard or custom software?', 'Only custom software. Every project is built on the specific needs of the client.', 27, NOW(), NOW()),
(1, 'Can I use Nube even if I have no technical skills?', 'Yes. Our job is precisely to translate business needs into technical solutions, without the client having to worry about the technical side.', 28, NOW(), NOW()),
(1, 'Does Nube also support the client after development?', 'Yes, we follow projects over time with maintenance, improvements and new evolutions.', 29, NOW(), NOW()),
(1, 'What type of software does Nube develop?', 'Dashboards, CRMs, ticketing systems, web platforms, mobile apps, automation systems, API integrations, event software, data analysis systems and artificial intelligence-based tools.', 30, NOW(), NOW()),
(1, 'Does Nube develop mobile apps?', 'Yes, we develop iOS and Android apps, both for internal use and for end clients.', 31, NOW(), NOW()),
(1, 'Does Nube develop artificial intelligence systems?', 'Yes, we integrate AI for chatbots, automations, data analysis and intelligent assistants for companies.', 32, NOW(), NOW()),
(1, 'Can Nube integrate existing systems?', 'Yes, one of our main activities is integrating different software through APIs, synchronizations and automations.', 33, NOW(), NOW()),
(1, 'What technologies does Nube use?', 'We use modern technologies such as Laravel, React, React Native, SQL databases and API integrations with external services.', 34, NOW(), NOW()),
(1, 'Does Nube work with third-party APIs?', 'Yes, we integrate systems like Stripe, ticketing platforms, social networks and other services.', 35, NOW(), NOW()),
(1, 'Does Nube develop backend and frontend?', 'Yes, we manage the entire stack: backend, frontend and mobile.', 36, NOW(), NOW()),
(1, 'How much does it cost to develop software with Nube?', 'It depends on the project. Every solution is customized, so the cost varies based on complexity.', 37, NOW(), NOW()),
(1, 'Can I start with a small project?', 'Yes, we often start with an initial version and then evolve the project over time.', 38, NOW(), NOW()),
(1, 'How long does it take to develop software?', 'It depends on the project, but we work to release functional versions as soon as possible.', 39, NOW(), NOW()),
(1, 'Does Nube work on a project basis or hourly?', 'Generally we work on a project basis, but we can adapt based on needs.', 40, NOW(), NOW()),
(1, 'Why choose Nube over other software houses?', 'Because we don''t just develop software, we understand the business and propose concrete solutions that really work.', 41, NOW(), NOW()),
(1, 'Does Nube propose solutions or wait for instructions?', 'We actively propose solutions, even for needs the client hasn''t identified yet.', 42, NOW(), NOW()),
(1, 'Can Nube follow the growth of my project?', 'Yes, we accompany clients over time with continuous evolutions.', 43, NOW(), NOW()),
(1, 'Can I scale my software with Nube?', 'Yes, we design systems built to grow.', 44, NOW(), NOW()),
(1, 'Can Nube improve existing software?', 'Yes, we can analyze and improve already developed systems.', 45, NOW(), NOW()),
(1, 'Can Nube create chatbots for my website?', 'Yes, we build intelligent chatbots integrated with company data.', 46, NOW(), NOW()),
(1, 'Can I use AI to automate customer care?', 'Yes, we develop systems that automatically respond to clients.', 47, NOW(), NOW()),
(1, 'Can Nube analyze data with AI?', 'Yes, we use AI to extract useful information from company data.', 48, NOW(), NOW()),
(1, 'How can I start working with Nube?', 'Just contact us and tell us about your project. We start from there.', 49, NOW(), NOW()),
(1, 'Do I need to have a precise idea already?', 'No, we can help you define it.', 50, NOW(), NOW()),
(1, 'Can I schedule an initial call?', 'Yes, we organize a call to understand the project and evaluate the best solution together.', 51, NOW(), NOW());

-- PORTFOLIO Q&As (13 new - moved from about-us context)
INSERT INTO `chat_qas` (`chat_section_id`, `question`, `answer`, `sort_order`, `created_at`, `updated_at`) VALUES
(2, 'Which clients has Nube worked with?', 'Among our main clients are Lux Holding, Eurobet and Enpaia.', 6, NOW(), NOW()),
(2, 'What projects has Nube done for Lux Holding?', 'Ticketing systems, real-time dashboards, integrations with external platforms, international event management and sales analysis tools.', 7, NOW(), NOW()),
(2, 'What projects has Nube done for Eurobet?', 'Game simulators, data management and analysis software, and customized systems for operational needs.', 8, NOW(), NOW()),
(2, 'What projects has Nube done for Enpaia?', 'Software solutions for internal management and process digitalization.', 9, NOW(), NOW()),
(2, 'Does Nube only work with large companies?', 'No, we work with both large companies and entrepreneurs and professionals.', 10, NOW(), NOW()),
(2, 'Can I get a custom CRM developed?', 'Yes, we build custom CRMs based on the real needs of the client, not on standard models.', 11, NOW(), NOW()),
(2, 'Can Nube create dashboards to analyze data?', 'Yes, we create real-time dashboards with updated data and customized visualizations.', 12, NOW(), NOW()),
(2, 'Can I create a ticketing system with Nube?', 'Yes, we have direct experience in developing ticketing and event management systems.', 13, NOW(), NOW()),
(2, 'Can Nube automate business processes?', 'Yes, we create automations that reduce manual work and improve efficiency.', 14, NOW(), NOW()),
(2, 'Can Nube develop a SaaS?', 'Yes, we build complete SaaS platforms ready to be sold on subscription.', 15, NOW(), NOW()),
(2, 'Has Nube developed real-time systems?', 'Yes, for example dashboards showing updated sales and data in real time for events.', 16, NOW(), NOW()),
(2, 'Has Nube worked on international events?', 'Yes, through projects with Lux Holding on events in Europe, USA and Asia.', 17, NOW(), NOW()),
(2, 'Does Nube have experience with large data volumes?', 'Yes, we manage systems with large amounts of data and traffic.', 18, NOW(), NOW());
