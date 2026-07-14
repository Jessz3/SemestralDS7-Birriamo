<?php

/**
 * Componente reutilizable para campos de contraseña.
 *
 * Variables esperadas:
 * $id
 * $name
 * $label
 * $required (bool)
 * $hint (opcional)
 */

$required = $required ?? true;
$hint = $hint ?? '';
?>

<div class="field">
    <label for="<?= htmlspecialchars($id) ?>">
        <?= htmlspecialchars($label) ?>
    </label>

    <div class="password-input-wrapper">

        <input
            type="password"
            id="<?= htmlspecialchars($id) ?>"
            name="<?= htmlspecialchars($name) ?>"
            <?= $required ? 'required' : '' ?>
        >

        <button
            type="button"
            class="password-toggle-btn"
            data-toggle-password="<?= htmlspecialchars($id) ?>"
            aria-label="Mostrar u ocultar contraseña"
            aria-pressed="false"
        >

            <svg class="eye-icon"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">

                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>

            <svg class="eye-off-icon"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2">

                <path d="M3 3l18 18"/>
                <path d="M10.6 10.6A3 3 0 0 0 13.4 13.4"/>
                <path d="M9 5.3A10.9 10.9 0 0 1 12 5c6.5 0 10 7 10 7"/>
                <path d="M6.3 6.3A18.7 18.7 0 0 0 2 12s3.5 7 10 7a10.9 10.9 0 0 0 4.9-1.2"/>
            </svg>

        </button>

    </div>

    <?php if ($hint !== ''): ?>
        <p class="field-hint">
            <?= htmlspecialchars($hint) ?>
        </p>
    <?php endif; ?>

</div>