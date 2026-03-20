<?php

namespace Database\Seeders;

use App\Models\ChatSection;
use App\Models\ChatQa;
use Illuminate\Database\Seeder;

class ChatQaSeeder extends Seeder
{
    public function run(): void
    {
        // ABOUT US
        $aboutUs = ChatSection::updateOrCreate(
            ['slug' => 'about-us'],
            ['name' => 'About Us', 'subtitle' => 'Learn about who we are and what we do', 'sort_order' => 1, 'active' => true]
        );

        $aboutUsQas = [
            [
                'question' => 'Who we are',
                'answer' => 'Nube is a partner that helps companies transform how they operate by deeply studying their business and identifying innovative solutions to address their needs, whether expressed or unexpressed. We approach each project with the goal of providing clear insights and developing technological solutions that revolutionize daily business operations.',
            ],
            [
                'question' => 'Our approach',
                'answer' => 'At Nube, we begin by thoroughly studying the internal processes and workflows of our clients. We analyze how they operate, identify needs—both obvious and hidden—and propose innovative solutions. Once we have a deep understanding of the client\'s reality, we develop tailored technologies to meet their needs and drive growth and efficiency in their business.',
            ],
            [
                'question' => 'What we do',
                'answer' => 'At Nube, we tackle the technological challenges of companies by first gaining a deep understanding of their business. We collaborate with large companies like RDS, Eurobet, and Lux Holding, listening to their needs, studying their operational context, and proposing customized technological solutions. Thanks to this flexible approach, we continuously learn new languages and techniques to deliver concrete results, leveraging advanced technologies such as artificial intelligence and automation to anticipate future needs.',
            ],
            [
                'question' => 'Our values',
                'answer' => 'Our work is based on attentive listening and in-depth analysis. We are partners to our clients, guiding them in discovering solutions that improve their work and optimize business processes. At Nube, every project stems from the belief that innovation and quality can radically transform the efficiency and potential of the companies we collaborate with.',
            ],
            [
                'question' => 'Our team',
                'answer' => 'The Nube team is made up of expert professionals who work closely with clients to understand every aspect of their business. We study how their processes function, identify areas for improvement, and develop tailored technological solutions, ready to meet current challenges and support future growth.',
            ],
            [
                'question' => 'Our commitment to clients',
                'answer' => 'At Nube, we are committed to being true allies to our clients. Every relationship begins with a phase of in-depth analysis and listening, during which we study business processes and operational dynamics. Only after this phase do we propose technological solutions that precisely meet the client\'s needs and develop projects that help companies grow and confidently face the future.',
            ],
        ];

        foreach ($aboutUsQas as $i => $qa) {
            ChatQa::updateOrCreate(
                ['chat_section_id' => $aboutUs->id, 'question' => $qa['question']],
                ['answer' => $qa['answer'], 'sort_order' => $i + 1]
            );
        }

        // PORTFOLIO
        $portfolio = ChatSection::updateOrCreate(
            ['slug' => 'portfolio'],
            ['name' => 'Portfolio', 'subtitle' => 'Discover our projects and case studies', 'sort_order' => 2, 'active' => true]
        );

        $portfolioQas = [
            [
                'question' => 'Automated ticketing system for RDS',
                'answer' => 'For RDS, one of the leading Italian radio stations, we developed an automated ticketing system for their Summer Festival. This event involved over 300,000 people across 6 different cities. We created a simple and intuitive interface for end users, with SSO login integration and immediate ticket generation with a QR code. The project also included the development of an app for the staff, which simplified and accelerated the check-in process at events, ensuring a smooth experience for both participants and the organization.',
            ],
            [
                'question' => 'Debugging and optimization for gaming platforms',
                'answer' => 'A leading multinational in the online gaming sector entrusted us with the task of optimizing and debugging their slot machine platform, which serves over 300,000 active players monthly. We stress-tested the platform, identifying and resolving critical bugs that could have compromised the gaming experience and caused disruptions for customers. Our intervention ensured the platform operated smoothly, improving reliability and security for players.',
            ],
            [
                'question' => 'Custom CRM for Lux Holding',
                'answer' => 'For Lux Holding, a multinational managing over 5 million tickets sold annually, we developed a custom CRM capable of centralizing data from various sources and analyzing market trends through integrated artificial intelligence. The system provided Lux Holding with a comprehensive and unified view of their operations, enabling them to make faster, more informed decisions. We also integrated a digital call center that allowed the team to manage communications from multiple platforms in a single interface.',
            ],
            [
                'question' => 'Mass WhatsApp messaging system for an internet service provider',
                'answer' => 'A major internet service provider needed to efficiently communicate with its customers to inform them about connection updates. We developed a mass messaging system via WhatsApp, which achieved a 99% delivery rate and an 80% open rate—significantly higher than traditional SMS, which only achieved a 30% open rate. This solution allowed the provider to improve communication effectiveness, with over 80% of contacted users completing their connection upgrade in record time.',
            ],
            [
                'question' => 'Innovative call center platform for Enpaia',
                'answer' => 'For Enpaia, the National Social Security Institution, we designed a VoIP call center platform that revolutionized the way the institution manages communications with its members. The platform allows operators to work remotely, handling calls and tickets from a cloud-based platform integrated with other features such as appointment scheduling via video calls. Thanks to our system, Enpaia improved operational flexibility and customer service quality, reducing wait times and optimizing resources.',
            ],
        ];

        foreach ($portfolioQas as $i => $qa) {
            ChatQa::updateOrCreate(
                ['chat_section_id' => $portfolio->id, 'question' => $qa['question']],
                ['answer' => $qa['answer'], 'sort_order' => $i + 1]
            );
        }

        // CONTACTS
        $contacts = ChatSection::updateOrCreate(
            ['slug' => 'contacts'],
            ['name' => 'Contacts', 'subtitle' => 'Get in touch with our team', 'sort_order' => 3, 'active' => true]
        );

        $contactsQas = [
            [
                'question' => 'Contact us via mobile',
                'answer' => 'You can reach us directly at +39 340 8538104. We are also available on Telegram and WhatsApp. Chat on WhatsApp: https://wa.me/393408538104 — Chat on Telegram: https://t.me/nubesoftware',
            ],
            [
                'question' => 'Contact us via email',
                'answer' => 'For any inquiries or information, you can also send us an email at info@nubelab.it.',
            ],
            [
                'question' => 'Follow us on social media',
                'answer' => 'Stay updated on our latest news and projects by following us on our social media channels: Instagram: https://www.instagram.com/nubesoftware — Facebook: https://www.facebook.com/nubesoftware',
            ],
        ];

        foreach ($contactsQas as $i => $qa) {
            ChatQa::updateOrCreate(
                ['chat_section_id' => $contacts->id, 'question' => $qa['question']],
                ['answer' => $qa['answer'], 'sort_order' => $i + 1]
            );
        }

        // BLOG (placeholder - will be configured later)
        ChatSection::updateOrCreate(
            ['slug' => 'blog'],
            ['name' => 'Blog', 'subtitle' => 'Read our latest articles and insights', 'sort_order' => 4, 'active' => false]
        );
    }
}
