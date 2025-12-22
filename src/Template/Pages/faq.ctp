<?php
/**
 * FAQ - Häufige Fragen und Begriffserklärungen
 * Inhalte werden aus config/help_texts.php geladen
 */

$faqItems = $this->HelpText->allFaq();

// Icon-Mapping für FAQ-Einträge
$icons = [
    'konto' => 'wallet2',
    'iban' => 'upc',
    'bic' => 'building',
    'kontostand' => 'cash-stack',
    'ueberweisung' => 'send',
    'auftrag' => 'clock-history',
    'umsaetze' => 'list-ul',
    'kontoauszug' => 'file-text',
    'tan' => 'shield-lock',
    'verwendungszweck' => 'chat-left-text',
    'limit' => 'speedometer2',
];
?>

<style>
/* Scroll-Offset für sticky Navbar (ca. 70px) */
.accordion-item[id] {
    scroll-margin-top: 80px;
}
</style>

<div class="row justify-content-center">
    <div class="col-lg-10 col-xl-8">

        <div class="mb-4">
            <h1><i class="bi bi-question-circle me-2"></i><?= h($this->HelpText->general('faq_title')) ?></h1>
            <p class="lead text-muted">
                <?= h($this->HelpText->general('faq_subtitle')) ?>
            </p>
        </div>

        <!-- Schnellnavigation -->
        <div class="card mb-4">
            <div class="card-body">
                <strong>Springe zu:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <?php foreach ($faqItems as $key => $item): ?>
                    <a href="#<?= $key ?>" class="btn btn-sm btn-outline-primary"><?= h(preg_replace('/^Was ist (eine?|der|die|das) /', '', $item['title'])) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- FAQ Einträge -->
        <div class="accordion" id="faqAccordion">
            <?php $first = true; ?>
            <?php foreach ($faqItems as $key => $item): ?>
            <div class="accordion-item" id="<?= $key ?>">
                <h2 class="accordion-header">
                    <button class="accordion-button <?= $first ? '' : 'collapsed' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?= $key ?>">
                        <i class="bi bi-<?= $icons[$key] ?? 'question-circle' ?> me-2 text-primary"></i>
                        <strong><?= h($item['title']) ?></strong>
                    </button>
                </h2>
                <div id="collapse-<?= $key ?>" class="accordion-collapse collapse <?= $first ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        <?= $this->HelpText->renderFaqContent($item['content']) ?>
                    </div>
                </div>
            </div>
            <?php $first = false; ?>
            <?php endforeach; ?>
        </div>

        <!-- Noch Fragen? -->
        <div class="card mt-4 border-primary">
            <div class="card-body text-center">
                <i class="bi bi-lightbulb text-primary fs-1 mb-3 d-block"></i>
                <h5><?= h($this->HelpText->general('faq_more_questions')) ?></h5>
                <p class="text-muted mb-0">
                    <?= h($this->HelpText->general('faq_contact')) ?>
                </p>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Alle Sprunglinks abfangen
    document.querySelectorAll('a[href^="#"]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            var targetId = this.getAttribute('href').substring(1);
            var target = document.getElementById(targetId);
            if (!target) return;

            e.preventDefault();

            // Accordion-Panel finden und öffnen
            var collapseId = 'collapse-' + targetId;
            var collapseEl = document.getElementById(collapseId);
            if (collapseEl && !collapseEl.classList.contains('show')) {
                var bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: true });
            }

            // Nach kurzem Delay scrollen (damit Accordion-Animation starten kann)
            setTimeout(function() {
                var navbarHeight = 80;
                var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }, 100);
        });
    });

    // Falls Seite mit Hash geladen wird
    if (window.location.hash) {
        var targetId = window.location.hash.substring(1);
        var target = document.getElementById(targetId);
        if (target) {
            var collapseId = 'collapse-' + targetId;
            var collapseEl = document.getElementById(collapseId);
            if (collapseEl) {
                var bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: true });
            }
            setTimeout(function() {
                var navbarHeight = 80;
                var targetPosition = target.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                window.scrollTo({ top: targetPosition, behavior: 'smooth' });
            }, 300);
        }
    }
});
</script>
