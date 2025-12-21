<?php
/**
 * EduBank - 400 Error Page (Not Found, Bad Request, etc.)
 *
 * @var string $message Error message
 * @var string $url Requested URL
 */
use Cake\Core\Configure;
use Cake\Error\Debugger;

$this->layout = 'error';

// Debug mode: use dev layout with detailed info
if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.ctp');

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
<?= $this->element('auto_table_warning') ?>
<?php
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
            <div class="rounded-circle bg-warning bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-4" style="width: 120px; height: 120px;">
                <i class="bi bi-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
            </div>
            <h1 class="display-4 fw-bold text-dark mb-2">404</h1>
            <h2 class="h4 text-muted mb-4">Seite nicht gefunden</h2>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <p class="text-muted mb-3">
                    Die angeforderte Seite konnte leider nicht gefunden werden. Das kann passieren, wenn:
                </p>
                <ul class="text-muted mb-0">
                    <li>Die Adresse falsch eingegeben wurde</li>
                    <li>Die Seite verschoben oder gelöscht wurde</li>
                    <li>Der Link veraltet ist</li>
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
    </div>
</div>

<?php else: ?>

<h2><?= h($message) ?></h2>
<p class="error">
    <strong><?= __d('cake', 'Error') ?>: </strong>
    <?= __d('cake', 'The requested address {0} was not found on this server.', "<strong>'{$url}'</strong>") ?>
</p>

<?php endif; ?>
