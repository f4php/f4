<?php

namespace App\Tutorial;

use F4\Core\CoreApiInterface;
use F4\Core\Route;
use F4\ModuleInterface;

class TutorialModule implements ModuleInterface
{
    public function __construct(CoreApiInterface &$f4)
    {
        // Tutorial homepage
        $f4->addRoute(Route::get('/tutorial', function() {
            return [
                'chapters' => $this->getChapters()
            ];
        })->setTemplate('tutorial/index.pug'));

        // Chapter 1: Hello World
        $f4->addRoute(Route::get('/tutorial/01-hello-world', function() {
            return [
                'message' => 'Hello from F4 Framework!',
                'timestamp' => date('Y-m-d H:i:s'),
                'chapter' => $this->getChapterInfo('01'),
            ];
        })->setTemplate('tutorial/chapter-01/chapter-01.pug'));

        // Chapter 2: Routing - Main page
        $f4->addRoute(Route::get('/tutorial/02-routing', function() {
            return [
                'chapter' => $this->getChapterInfo('02'),
            ];
        })->setTemplate('tutorial/chapter-02/chapter-02.pug'));

        // Chapter 2: Routing - Demo routes
        $f4->addRoute(Route::get('/tutorial/02-routing/demo/{id:int}', function(int $id) {
            return [
                'id' => $id,
                'type' => 'integer',
                'chapter' => $this->getChapterInfo('02'),
            ];
        })->setTemplate('tutorial/chapter-02/demo.pug'));

        $f4->addRoute(Route::get('/tutorial/02-routing/demo/{slug:string}', function(string $slug) {
            return [
                'slug' => $slug,
                'type' => 'string',
                'chapter' => $this->getChapterInfo('02'),
            ];
        })->setTemplate('tutorial/chapter-02/demo.pug'));

        // Chapter 3: Templates
        $f4->addRoute(Route::get('/tutorial/03-templates', function() {
            return [
                'chapter' => $this->getChapterInfo('03'),
            ];
        })->setTemplate('tutorial/chapter-03/chapter-03.pug'));

        // Chapter 4: Validation
        $f4->addRoute(Route::get('/tutorial/04-validation', function() {
            return [
                'chapter' => $this->getChapterInfo('04'),
            ];
        })->setTemplate('tutorial/chapter-04/chapter-04.pug'));

        // Chapter 5: Middleware
        $f4->addRoute(Route::get('/tutorial/05-middleware', function() {
            return [
                'chapter' => $this->getChapterInfo('05'),
            ];
        })->setTemplate('tutorial/chapter-05/chapter-05.pug'));

        // Chapter 6: Route Groups
        $f4->addRoute(Route::get('/tutorial/06-route-groups', function() {
            return [
                'chapter' => $this->getChapterInfo('06'),
            ];
        })->setTemplate('tutorial/chapter-06/chapter-06.pug'));

        // Chapter 7: Database
        $f4->addRoute(Route::get('/tutorial/07-database', function() {
            return [
                'chapter' => $this->getChapterInfo('07'),
            ];
        })->setTemplate('tutorial/chapter-07/chapter-07.pug'));

        // Chapter 8: i18n
        $f4->addRoute(Route::get('/tutorial/08-i18n', function() {
            return [
                'chapter' => $this->getChapterInfo('08'),
            ];
        })->setTemplate('tutorial/chapter-08/chapter-08.pug'));

        // Chapter 9: Exceptions
        $f4->addRoute(Route::get('/tutorial/09-exceptions', function() {
            return [
                'chapter' => $this->getChapterInfo('09'),
            ];
        })->setTemplate('tutorial/chapter-09/chapter-09.pug'));

        // Chapter 10: Configuration
        $f4->addRoute(Route::get('/tutorial/10-configuration', function() {
            return [
                'chapter' => $this->getChapterInfo('10'),
            ];
        })->setTemplate('tutorial/chapter-10/chapter-10.pug'));

        // Chapter 11: Debug Mode
        $f4->addRoute(Route::get('/tutorial/11-debug-mode', function() {
            return [
                'chapter' => $this->getChapterInfo('11'),
            ];
        })->setTemplate('tutorial/chapter-11/chapter-11.pug'));

        // Chapter 12: Deployment
        $f4->addRoute(Route::get('/tutorial/12-deployment', function() {
            return [
                'chapter' => $this->getChapterInfo('12'),
            ];
        })->setTemplate('tutorial/chapter-12/chapter-12.pug'));
    }

    private function getChapters(): array
    {
        return [
            [
                'number' => '01',
                'slug' => 'hello-world',
                'title' => 'Hello World',
                'description' => 'Basic routing and templates',
                'path' => '/tutorial/01-hello-world',
            ],
            [
                'number' => '02',
                'slug' => 'routing',
                'title' => 'Routing Patterns',
                'description' => 'Path parameters and HTTP methods',
                'path' => '/tutorial/02-routing',
            ],
            [
                'number' => '03',
                'slug' => 'templates',
                'title' => 'Working with Templates',
                'description' => 'Pug syntax and PHP expressions',
                'path' => '/tutorial/03-templates',
            ],
            [
                'number' => '04',
                'slug' => 'validation',
                'title' => 'Parameter Validation',
                'description' => 'Attributes and sanitization',
                'path' => '/tutorial/04-validation',
            ],
            [
                'number' => '05',
                'slug' => 'middleware',
                'title' => 'Middleware',
                'description' => 'Request pipeline and filters',
                'path' => '/tutorial/05-middleware',
            ],
            [
                'number' => '06',
                'slug' => 'route-groups',
                'title' => 'Route Groups',
                'description' => 'API design and organization',
                'path' => '/tutorial/06-route-groups',
            ],
            [
                'number' => '07',
                'slug' => 'database',
                'title' => 'Database',
                'description' => 'Query builder and CRUD',
                'path' => '/tutorial/07-database',
            ],
            [
                'number' => '08',
                'slug' => 'i18n',
                'title' => 'Internationalization',
                'description' => 'Multi-language support',
                'path' => '/tutorial/08-i18n',
            ],
            [
                'number' => '09',
                'slug' => 'exceptions',
                'title' => 'Exception Handling',
                'description' => 'Error handling and custom pages',
                'path' => '/tutorial/09-exceptions',
            ],
            [
                'number' => '10',
                'slug' => 'configuration',
                'title' => 'Configuration',
                'description' => 'Config files and environments',
                'path' => '/tutorial/10-configuration',
            ],
            [
                'number' => '11',
                'slug' => 'debug-mode',
                'title' => 'Debug Mode',
                'description' => 'Built-in debugger and profiling',
                'path' => '/tutorial/11-debug-mode',
            ],
            [
                'number' => '12',
                'slug' => 'deployment',
                'title' => 'Deployment',
                'description' => 'Production builds and deployment',
                'path' => '/tutorial/12-deployment',
            ],
        ];
    }

    private function getChapterInfo(string $number): array
    {
        $chapters = $this->getChapters();
        $currentIndex = null;

        foreach ($chapters as $index => $chapter) {
            if ($chapter['number'] === $number) {
                $currentIndex = $index;
                break;
            }
        }

        if ($currentIndex === null) {
            return [];
        }

        return [
            'current' => $chapters[$currentIndex],
            'previous' => $currentIndex > 0 ? $chapters[$currentIndex - 1] : null,
            'next' => isset($chapters[$currentIndex + 1]) ? $chapters[$currentIndex + 1] : null,
            'all' => $chapters,
        ];
    }
}
