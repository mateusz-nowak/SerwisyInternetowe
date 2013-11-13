<?php

use Bootstrap\Resources\DependencyContainer;
use CoreBundle\Exception404;
use CoreBundle\Bootstrap;
use AuthBundle\Event\Auth as EventAuth;
use BlogBundle\Event\Blog as EventBlog;
use BlogBundle\ValueObject\BlogModifier;

include_once '../vendor/autoload.php';

$app = new Bootstrap();
$container = new DependencyContainer();

$app->get('/', function() use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');

    return $template->render('src/BlogBundle/Views/main/index.html', array(
        'title' => $i18n->get('user.heading.main')
    ));
});

/**
 * Register new user.
**/
$app->get('/user/new', function() use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $formRegister = $container->get('form.register');

    return $template->render('src/AuthBundle/Views/user/new.html', array(
        'title' => $i18n->get('user.heading.register'),
        'form' => $formRegister
    ));
});

$app->post('/user', function() use ($container) {
    $template = $container->get('template');
    $request = $container->get('request');
    $i18n = $container->get('i18n');

    $formRegister = $container->get('form.register');
    $formRegister->bind($request->post);

    if ($formRegister->isValid()) {
        return $container
            ->get('dispatcher')
            ->trigger(EventAuth::IS_REGISTERED, $formRegister);
    }

    return $template->render('src/AuthBundle/Views/user/new.html', array(
        'title' => $i18n->get('user.heading.register'),
        'form' => $formRegister
    ));
});

/**
 * Log in.
 * Create new session if user exists in database.
**/
$app->get('/session/new', function() use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $formLogin = $container->get('form.login');

    return $template->render('src/AuthBundle/Views/user/login.html', array(
        'title' => $i18n->get('user.heading.login'),
        'form' => $formLogin
    ));
});

$app->post('/session', function() use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $request = $container->get('request');
    $formLogin = $container->get('form.login');
    
    $formLogin->bind($request->post);
    
    if ($formLogin->isValid()) {
        return $container
            ->get('dispatcher')
            ->trigger(EventAuth::IS_LOGGED, $formLogin->getUserInstance());
    }

    return $template->render('src/AuthBundle/Views/user/login.html', array(
        'title' => $i18n->get('user.heading.login'),
        'form' => $formLogin
    ));
});

/**
 * Logout
**/
$app->get('/session/destroy', function() use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    
    return $container
        ->get('dispatcher')
        ->trigger(EventAuth::IS_LOGGED_OUT);
});

/**
 * User - My Blogs
**/
$app->get('/user/blogs', function() use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $request = $container->get('request');
    
    $blogManager = $container->get('blog.manager');
    $securityContext = $container->get('security.context');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);

    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);

    return $template->render('src/BlogBundle/Views/blogs/index.html', array(
        'title' => $i18n->get('user.heading.blogs'),
        'blogs' => $blogManager->findAllByUser($securityContext->getUser())
    ));
});

/**
 * User - Add new blog
**/
$app->get('/user/blogs/new', function() use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);
    
    $form = $container->get('form.blogs.new');

    return $template->render('src/BlogBundle/Views/blogs/new.html', array(
        'title' => $i18n->get('user.heading.blogs.new'),
        'form' => $form
    ));
});

$app->post('/user/blogs', function() use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $request = $container->get('request');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);
    
    $form = $container->get('form.blogs.new');
    $form->bind($request->post);
    
    if ($form->isValid()) {
        $container
            ->get('dispatcher')
            ->trigger(EventBlog::IS_CREATED, $form);
    }
    
    return $template->render('src/BlogBundle/Views/blogs/new.html', array(
        'title' => $i18n->get('user.heading.blogs.new'),
        'form' => $form
    ));
});

/**
 * User - Destroy Blog
**/
$app->get('/user/blog/(?P<id>\d+)/destroy', function($requestParameters) use ($container) {
    $i18n = $container->get('i18n');
    $blogManager = $container->get('blog.manager');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);
    
    $blog = $blogManager->findOneById($requestParameters['id']);

    if ($blog) {
        $container
            ->get('dispatcher')
            ->trigger(EventBlog::IS_DESTROYED, $blog);
    }
});

/**
 * User - Edit Blog
**/
$app->get('/user/blog/(?P<id>\d+)/edit', function($requestParameters) use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $blogManager = $container->get('blog.manager');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);
        
    $blog = $blogManager->findOneById($requestParameters['id']);
    
    if (!$blog) {
        return;
    }
    
    $form = $container->get('form.blogs.new');
    $form->bind($blog->toArray());
    
    return $template->render('src/BlogBundle/Views/blogs/edit.html', array(
        'title' => $blog->getTitle(),
        'form' => $form,
        'blog' => $blog
    ));
});

$app->post('/user/blog/(?P<id>\d+)', function($requestParameters) use ($container) {
    $template = $container->get('template');
    $request = $container->get('request');
    $i18n = $container->get('i18n');
    $blogManager = $container->get('blog.manager');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);
    
    $blog = $blogManager->findOneById($requestParameters['id']);
    
    if (!$blog) {
        return;
    }
    
    $form = $container->get('form.blogs.new');
    $form->bind($request->post);
    
    if ($form->isValid()) {
        return $container
            ->get('dispatcher')
            ->trigger(EventBlog::IS_EDITED, 
                new BlogModifier($blog, $form)
            );
    }
    
    return $template->render('src/BlogBundle/Views/blogs/edit.html', array(
        'title' => $blog->getTitle(),
        'form' => $form,
        'blog' => $blog
    ));
});

/**
 * User - Manage blog posts.
**/
$app->get('/user/blog/(?P<id>\d+)/manage', function($requestParameters) use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $blogManager = $container->get('blog.manager');

    $blog = $blogManager->findOneById($requestParameters['id']);
    
    if (!$blog) {
        return;
    }

    return $template->render('src/BlogBundle/Views/blogs/manage/index.html', array(
        'title' => $i18n->get('user.heading.manager'),
        'blog' => $blog
    ));
});

try {
    $app->run();
} catch (Exception404 $e) {
    $template = $container->get('template');
    
    echo $template->render('src/SiBundle/Views/error/error.html', array(
        'e' => $e,
        'title' => '404'
    ));
}
