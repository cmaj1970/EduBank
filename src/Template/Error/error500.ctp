<?php
/**
 * EduBank - 500 Error Page (Internal Server Error)
 *
 * @var string $message Error message
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'error';

// Debug mode: use dev layout with detailed info
if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.ctp');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
<?php endif; ?>
<?php if ($error instanceof Error) : ?>
        <strong>Error in: </strong>
        <?= sprintf('%s, line %s', str_replace(ROOT, 'ROOT', $error->getFile()), $error->getLine()) ?>
<?php endif; ?>
<?php
    echo $this->element('auto_table_warning');

    if (extension_loaded('xdebug')) :
        xdebug_print_function_stack();
    endif;

    $this->end();
endif;

// Production mode: nice styled error page
if (!Configure::read('debug')) :
?>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="text-center mb-4">
            <div class="rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 120px; height: 120px;">
                <i class="bi bi-x-circle text-danger" style="font-size: 4rem;"></i>
            </div>
            <h1 class="display-4 fw-bold text-dark mb-2">500</h1>
            <h2 class="h4 text-muted mb-4">Ein Fehler ist aufgetreten</h2>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-octagon me-2"></i>
                    <strong>Interner Serverfehler</strong>
                </div>
                <p class="text-muted mb-3">
                    Bei der Verarbeitung Ihrer Anfrage ist leider ein unerwarteter Fehler aufgetreten.
                </p>
                <p class="text-muted mb-0">
                    <strong>Was Sie tun können:</strong>
                </p>
                <ul class="text-muted mb-0">
                    <li>Versuchen Sie es in einigen Minuten erneut</li>
                    <li>Kehren Sie zur Startseite zurück</li>
                    <li>Kontaktieren Sie den Administrator, falls das Problem weiterhin besteht</li>
                </ul>
            </div>
        </div>

        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
            <a href="javascript:history.back()" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Zurück
            </a>
            <a href="/" class="btn btn-primary">
                <i class="bi bi-house me-2"></i>Zur Startseite
            </a>
        </div>

        <div class="text-center mt-4">
            <p class="text-muted small mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Fehler-ID: <?= substr(md5(time() . rand()), 0, 8) ?>
            </p>
        </div>
    </div>
</div>

<?php else: ?>

<h2><?= __d('cake', 'An Internal Error Has Occurred') ?></h2>
<p class="error">
    <strong><?= __d('cake', 'Error') ?>: </strong>
    <?= h($message) ?>
</p>

<?php endif; ?>
