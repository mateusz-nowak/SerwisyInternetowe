<?php

use Bootstrap\Resources\DependencyContainer;
use CoreBundle\Exception404;
use CoreBundle\Bootstrap;
use AuthBundle\Event\Auth as EventAuth;
use BlogBundle\Event\Blog as EventBlog;
use BlogBundle\ValueObject\BlogModifier;
use BlogBundle\ValueObject\PostModifier;
use BlogBundle\Event\PostEvent;

include_once '../vendor/autoload.php';

$app = new Bootstrap();
$container = new DependencyContainer();

/**
 * Executes before some response is given.
**/

$template = $container->get('template');
$blogManager = $container->get('blog.manager');
$userManager = $container->get('user.manager');

$template->setAttribute('latestBlog', $blogManager->getLast(10));
$template->setAttribute('latestUsers', $userManager->getLast(10));

/**
 * Homepage.
**/
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
    
    $breadcrumb = $container->get('breadcrumb');
    $breadcrumb->attach('/user/blogs', $i18n->get('title.user.blogs'));
    $breadcrumb->attach('/user/blogs/new', $i18n->get('title.user.blogs.new'));
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);
    
    $form = $container->get('form.blogs.new');

    return $template->render('src/BlogBundle/Views/blogs/new.html', array(
        'title' => $i18n->get('user.heading.blogs.new'),
        'form' => $form,
        'breadcrumb' => $breadcrumb
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

    $breadcrumb = $container->get('breadcrumb');
    $breadcrumb->attach('/user/blogs', $i18n->get('title.user.blogs'));
    $breadcrumb->attach('/user/blog/' . $blog->getId() . '/edit', $i18n->get('title.user.blogs.edit'));
    
    $form = $container->get('form.blogs.new');
    $form->bind($blog->toArray());
    
    return $template->render('src/BlogBundle/Views/blogs/edit.html', array(
        'title' => $blog->getTitle(),
        'form' => $form,
        'blog' => $blog,
        'breadcrumb' => $breadcrumb
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
    $postManager = $container->get('post.manager');
    $blogManager = $container->get('blog.manager');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);

    $blog = $blogManager->findOneById($requestParameters['id']);
    
    if (!$blog) {
        return;
    }
    
    $posts = $postManager->findBlogPosts($blog);
    
    $breadcrumb = $container->get('breadcrumb');
    $breadcrumb->attach('/user/blogs', $i18n->get('title.user.blogs'));
    $breadcrumb->attach('/user/blog/' . $blog->getId() . '/manage', $blog->getTitle());

    return $template->render('src/BlogBundle/Views/blogs/manage/index.html', array(
        'title' => $i18n->get('user.heading.manager'),
        'blog' => $blog,
        'posts' => $posts,
        'breadcrumb' => $breadcrumb
    ));
});

/**
 * User - Add new post to blog
**/
$app->get('/user/blog/(?P<id>\d+)/manage/new', function($requestParameters) use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $blogManager = $container->get('blog.manager');
    $form = $container->get('form.blog.posts.new');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);
    
    $blog = $blogManager->findOneById($requestParameters['id']);
    
    $breadcrumb = $container->get('breadcrumb');
    $breadcrumb->attach('/user/blogs', $i18n->get('title.user.blogs'));
    $breadcrumb->attach('/user/blog/' . $blog->getId() . '/manage', $blog->getTitle());
    $breadcrumb->attach('/user/blog/' . $blog->getId() . '/manage/new', $i18n->get('title.blogs.posts.new'));
    
    return $template->render('src/BlogBundle/Views/blogs/manage/new.html', array(
        'title' => $i18n->get('user.heading.manager.post.new'),
        'form' => $form,
        'blog' => $blog,
        'breadcrumb' => $breadcrumb
    ));
});

$app->get('/user/blog/(?P<blog_id>\d+)/post/(?P<id>\d+)/edit', function($requestParameters) use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $postManager = $container->get('post.manager');
    $blogManager = $container->get('blog.manager');
    $form = $container->get('form.blog.posts.new');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);
    
    $post = $postManager->findOneById($requestParameters['id']);
    $blog = $blogManager->findOneById($requestParameters['blog_id']);
    
    $form->bind($post->toFormArray());
    
    $breadcrumb = $container->get('breadcrumb');
    $breadcrumb->attach('/user/blogs', $i18n->get('title.user.blogs'));
    $breadcrumb->attach('/user/blog/' . $blog->getId() . '/manage', $blog->getTitle());
    $breadcrumb->attach('/user/blog/' . $blog->getId() . '/post/' . $post->getId() .'/edit', $post->getTitle());
    
    return $template->render('src/BlogBundle/Views/blogs/manage/edit.html', array(
        'title' => $i18n->get('user.heading.manager.post.edit'),
        'form' => $form,
        'post' => $post,
        'blog' => $blog,
        'breadcrumb' => $breadcrumb
    ));
});

$app->post('/user/blog/(?P<blog_id>\d+)/post/(?P<id>\d+)', function($requestParameters) use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $postManager = $container->get('post.manager');
    $blogManager = $container->get('blog.manager');
    $form = $container->get('form.blog.posts.new');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);
    
    $post = $postManager->findOneById($requestParameters['id']);
    $blog = $blogManager->findOneById($requestParameters['blog_id']);
    
    $request = $container->get('request');
    $form->bind($request->post);
    
    $post->setBlog($blog);
    
    $postModifier = new PostModifier($post, $form);
    
    if ($form->isValid()) {
        return $container
            ->get('dispatcher')
            ->trigger(EventBlog::IS_POST_EDITED, $postModifier);
    }
    
    return $template->render('src/BlogBundle/Views/blogs/manage/edit.html', array(
        'title' => $i18n->get('user.heading.manager.new'),
        'form' => $form,
        'post' => $post,
        'blog' => $blog
    ));
});

$app->post('/user/blog/(?P<id>\d+)/manage', function($requestParameters) use ($container) {
    $template = $container->get('template');
    $i18n = $container->get('i18n');
    $blogManager = $container->get('blog.manager');
    $form = $container->get('form.blog.posts.new');
    $request = $container->get('request');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);

    $blog = $blogManager->findOneById($requestParameters['id']);
    
    $form->bind($request->post);
    
    if ($form->isValid()) {
        return $container
            ->get('dispatcher')
            ->trigger(EventBlog::IS_NEW_POST_CREATED, 
                new PostEvent($blog, $form)
            );
    }
    
    return $template->render('src/BlogBundle/Views/blogs/manage/new.html', array(
        'title' => $i18n->get('user.heading.manager.new'),
        'form' => $form,
        'blog' => $blog
    ));
});


/**
 * User - Destroy post in blog
**/
$app->get('/user/blog/(?P<blog_id>\d+)/post/(?P<id>\d+)/destroy', function($requestParameters) use ($container) {
    $i18n = $container->get('i18n');
    $postManager = $container->get('post.manager');
    
    $container
        ->get('dispatcher')
        ->trigger(EventAuth::CHECK_AUTHENTICATION);

    $post = $postManager->findOneById($requestParameters['id']);

    if ($post) {
        $container
            ->get('dispatcher')
            ->trigger(EventBlog::IS_POST_DESTROYED, $post);
    }
});

/**
 * Blog - Display blog
**/
$app->get('/b/(?P<domain>.*)', function($requestParameters) use ($container, $template) {
    $blogManager = $container->get('blog.manager');
    $postManager = $container->get('post.manager');
    
    $blog = $blogManager->findOneByDomain($requestParameters['domain']);
    $posts = $postManager->findBlogPosts($blog);
    
    return $template->render('src/BlogBundle/Views/blogs/show.html', array(
        'title' => $blog->getTitle(),
        'blog' => $blog,
        'posts' => $posts
    ));
});

try {
    $app->run();
} catch (Exception404 $e) {
    $template = $container->get('template');
    
    echo $template->render('app/Bootstrap/Views/error/error.html', array(
        'e' => $e,
        'title' => '[404]'
    ));
}