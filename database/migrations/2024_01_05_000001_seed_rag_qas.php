<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $aboutUsId = DB::table('chat_sections')->where('slug', 'about-us')->value('id');
        $portfolioId = DB::table('chat_sections')->where('slug', 'portfolio')->value('id');

        if (!$aboutUsId || !$portfolioId) return;

        $aboutQas = [
            ['Who is Nube really?', 'Nube is not a typical software house. It is a technology partner that gets to the heart of clients\' businesses to build solutions that truly work in the real world. We don\'t just develop software: we design tools that change the way companies operate.'],
            ['What makes Nube different from other software companies?', 'The difference lies in how we approach problems. We don\'t start from technology, but from the business. We analyze how the client works, identify inefficiencies, opportunities and growth levers, and then build tailored solutions that generate concrete results.'],
            ['Is Nube more development-oriented or strategy-oriented?', 'Both. Technology is just a tool. The real value lies in the ability to understand what is truly needed and transform it into a working, scalable and sustainable system.'],
            ['What mindset does Nube have?', 'Pragmatic. Every decision is results-oriented. We don\'t build software that looks good but software that gets used, solves problems and generates value.'],
            ['Does Nube work as a vendor or a partner?', 'As a partner. We enter projects with a long-term vision, taking responsibility for the solutions we develop.'],
            ['What is Nube\'s vision?', 'Creating technological solutions that anticipate company needs, even when they are not yet obvious. The goal is not to chase requests, but to help clients evolve.'],
            ['What does anticipating needs mean?', 'It means proposing solutions the client hasn\'t asked for yet, but that become obvious once implemented. This is one of the main advantages of working with Nube.'],
            ['Does Nube follow a standard methodology?', 'No. Every project is different, so the method adapts too. The only constant is the focus on results.'],
            ['Is Nube suitable for any client?', 'No. It is ideal for those who want to build something solid, customized and with a real impact on their business.'],
            ['Is Nube suitable for complex projects?', 'Yes, it is precisely in those contexts that it delivers the most value.'],
            ['Does Nube work with companies that want to grow?', 'Yes. The best results come from clients who see technology as a strategic lever, not just a cost.'],
            ['What impact does Nube\'s work have on clients?', 'Process improvement, increased efficiency, error reduction, greater data control and new business opportunities.'],
            ['Does Nube just create software or change the way of working?', 'It changes the way of working. Software is just the means.'],
            ['What is Nube\'s philosophy?', 'If software isn\'t used, it\'s useless. If it doesn\'t improve something, it\'s wrong. If it isn\'t designed to scale, it\'s limiting.'],
            ['How does Nube make decisions during a project?', 'Always based on the impact on the client\'s business, not on technical convenience.'],
            ['Why should I choose Nube?', 'Because you\'re not looking for someone who writes code. You\'re looking for someone who understands your business and builds something that makes it work better, grow and generate value over time.'],
            ['What is Nube?', 'Nube is a software house that develops custom digital solutions for companies, entrepreneurs and professionals. We don\'t just build software, we enter the client\'s business to understand how they work and create tools that solve real problems or anticipate unexpressed needs.'],
            ['What type of company is Nube?', 'Nube is a technology company focused on custom software development, with a strong focus on automation, system integration, data analysis and innovative solutions.'],
            ['What is Nube\'s objective?', 'The objective is to help companies work better, reduce inefficiencies and create competitive advantages through technology.'],
            ['How does the work process with Nube function?', 'We always start with a phase of understanding the client\'s business. We study how they work, identify problems and opportunities, and then design a custom solution. After design, we develop the software and support the client in subsequent phases.'],
            ['Does Nube develop standard or custom software?', 'Only custom software. Every project is built on the specific needs of the client.'],
            ['Can I use Nube even if I have no technical skills?', 'Yes. Our job is precisely to translate business needs into technical solutions, without the client having to worry about the technical side.'],
            ['Does Nube also support the client after development?', 'Yes, we follow projects over time with maintenance, improvements and new evolutions.'],
            ['What type of software does Nube develop?', 'Dashboards, CRMs, ticketing systems, web platforms, mobile apps, automation systems, API integrations, event software, data analysis systems and artificial intelligence-based tools.'],
            ['Does Nube develop mobile apps?', 'Yes, we develop iOS and Android apps, both for internal use and for end clients.'],
            ['Does Nube develop artificial intelligence systems?', 'Yes, we integrate AI for chatbots, automations, data analysis and intelligent assistants for companies.'],
            ['Can Nube integrate existing systems?', 'Yes, one of our main activities is integrating different software through APIs, synchronizations and automations.'],
            ['What technologies does Nube use?', 'We use modern technologies such as Laravel, React, React Native, SQL databases and API integrations with external services.'],
            ['Does Nube work with third-party APIs?', 'Yes, we integrate systems like Stripe, ticketing platforms, social networks and other services.'],
            ['Does Nube develop backend and frontend?', 'Yes, we manage the entire stack: backend, frontend and mobile.'],
            ['How much does it cost to develop software with Nube?', 'It depends on the project. Every solution is customized, so the cost varies based on complexity.'],
            ['Can I start with a small project?', 'Yes, we often start with an initial version and then evolve the project over time.'],
            ['How long does it take to develop software?', 'It depends on the project, but we work to release functional versions as soon as possible.'],
            ['Does Nube work on a project basis or hourly?', 'Generally we work on a project basis, but we can adapt based on needs.'],
            ['Why choose Nube over other software houses?', 'Because we don\'t just develop software, we understand the business and propose concrete solutions that really work.'],
            ['Does Nube propose solutions or wait for instructions?', 'We actively propose solutions, even for needs the client hasn\'t identified yet.'],
            ['Can Nube follow the growth of my project?', 'Yes, we accompany clients over time with continuous evolutions.'],
            ['Can I scale my software with Nube?', 'Yes, we design systems built to grow.'],
            ['Can Nube improve existing software?', 'Yes, we can analyze and improve already developed systems.'],
            ['Can Nube create chatbots for my website?', 'Yes, we build intelligent chatbots integrated with company data.'],
            ['Can I use AI to automate customer care?', 'Yes, we develop systems that automatically respond to clients.'],
            ['Can Nube analyze data with AI?', 'Yes, we use AI to extract useful information from company data.'],
            ['How can I start working with Nube?', 'Just contact us and tell us about your project. We start from there.'],
            ['Do I need to have a precise idea already?', 'No, we can help you define it.'],
            ['Can I schedule an initial call?', 'Yes, we organize a call to understand the project and evaluate the best solution together.'],
        ];

        $portfolioQas = [
            ['Which clients has Nube worked with?', 'Among our main clients are Lux Holding, Eurobet and Enpaia.'],
            ['What projects has Nube done for Lux Holding?', 'Ticketing systems, real-time dashboards, integrations with external platforms, international event management and sales analysis tools.'],
            ['What projects has Nube done for Eurobet?', 'Game simulators, data management and analysis software, and customized systems for operational needs.'],
            ['What projects has Nube done for Enpaia?', 'Software solutions for internal management and process digitalization.'],
            ['Does Nube only work with large companies?', 'No, we work with both large companies and entrepreneurs and professionals.'],
            ['Can I get a custom CRM developed?', 'Yes, we build custom CRMs based on the real needs of the client, not on standard models.'],
            ['Can Nube create dashboards to analyze data?', 'Yes, we create real-time dashboards with updated data and customized visualizations.'],
            ['Can I create a ticketing system with Nube?', 'Yes, we have direct experience in developing ticketing and event management systems.'],
            ['Can Nube automate business processes?', 'Yes, we create automations that reduce manual work and improve efficiency.'],
            ['Can Nube develop a SaaS?', 'Yes, we build complete SaaS platforms ready to be sold on subscription.'],
            ['Has Nube developed real-time systems?', 'Yes, for example dashboards showing updated sales and data in real time for events.'],
            ['Has Nube worked on international events?', 'Yes, through projects with Lux Holding on events in Europe, USA and Asia.'],
            ['Does Nube have experience with large data volumes?', 'Yes, we manage systems with large amounts of data and traffic.'],
        ];

        $sort = (int) DB::table('chat_qas')->where('chat_section_id', $aboutUsId)->max('sort_order') ?? 0;
        foreach ($aboutQas as $qa) {
            $exists = DB::table('chat_qas')->where('chat_section_id', $aboutUsId)->where('question', $qa[0])->exists();
            if (!$exists) {
                $sort++;
                DB::table('chat_qas')->insert([
                    'chat_section_id' => $aboutUsId,
                    'question' => $qa[0],
                    'answer' => $qa[1],
                    'sort_order' => $sort,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $sort = (int) DB::table('chat_qas')->where('chat_section_id', $portfolioId)->max('sort_order') ?? 0;
        foreach ($portfolioQas as $qa) {
            $exists = DB::table('chat_qas')->where('chat_section_id', $portfolioId)->where('question', $qa[0])->exists();
            if (!$exists) {
                $sort++;
                DB::table('chat_qas')->insert([
                    'chat_section_id' => $portfolioId,
                    'question' => $qa[0],
                    'answer' => $qa[1],
                    'sort_order' => $sort,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        // Non rimuoviamo le Q&A nel rollback
    }
};
