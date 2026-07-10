<section
  class="messages-page<?= $isThreadOpen ? ' messages-page--thread-open' : '' ?>"
  aria-label="Messagerie">
  <aside class="messages-page__sidebar" aria-label="Conversations">
    <h1 class="messages-page__title">Messagerie</h1>

    <ul class="messages-page__conversation-list">
      <?php foreach ($conversations as $conversation): ?>
        <li>
          <a
            class="messages-page__conversation<?= $conversation['active'] ? ' messages-page__conversation--active' : '' ?>"
            href="index.php?page=messages&amp;conversation=<?= urlencode($conversation['uuid']) ?>"
            <?= $conversation['active'] ? 'aria-current="page"' : '' ?>>
            <img
              class="messages-page__avatar"
              src="<?= htmlspecialchars($conversation['picture']) ?>"
              alt="">

            <span class="messages-page__conversation-content">
              <span class="messages-page__conversation-heading">
                <span class="messages-page__conversation-name">
                  <?= htmlspecialchars($conversation['pseudo']) ?>
                </span>
                <time class="messages-page__conversation-time">
                  <?= htmlspecialchars($conversation['time']) ?>
                </time>
              </span>

              <span class="messages-page__conversation-preview">
                <?= htmlspecialchars($conversation['preview']) ?>
              </span>
            </span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <section
    class="messages-page__thread"
    aria-label="<?= $activeConversation !== null ? 'Conversation avec ' . htmlspecialchars($activeConversation['pseudo']) : 'Conversation' ?>"
    <?php if ($activeConversation !== null): ?>
      data-conversation-uuid="<?= htmlspecialchars($activeConversation['uuid']) ?>"
      data-poll-url="index.php?page=messages-poll&amp;conversation=<?= urlencode($activeConversation['uuid']) ?>"
      data-send-url="index.php?page=messages"
    <?php endif; ?>>
    <a class="messages-page__back-link" href="index.php?page=messages">
      &lt; retour
    </a>

    <?php if ($activeConversation !== null): ?>
      <header class="messages-page__thread-header">
        <img
          class="messages-page__thread-avatar"
          src="<?= htmlspecialchars($activeConversation['picture']) ?>"
          alt="">
        <h2 class="messages-page__thread-name">
          <?= htmlspecialchars($activeConversation['pseudo']) ?>
        </h2>
      </header>
    <?php else: ?>
      <header class="messages-page__thread-header messages-page__thread-header--empty">
        <h2 class="messages-page__thread-name">
          Sélectionnez une conversation
        </h2>
      </header>
    <?php endif; ?>

    <div class="messages-page__messages" aria-label="Messages" data-messages-list>
      <?php if ($activeConversation === null): ?>
        <p class="messages-page__empty">
          Choisissez une conversation ou démarrez-en une depuis le profil d'un lecteur.
        </p>
      <?php elseif (empty($messages)): ?>
        <p class="messages-page__empty">
          Aucun message pour le moment.
        </p>
      <?php endif; ?>

      <?php foreach ($messages as $message): ?>
        <article
          class="messages-page__message messages-page__message--<?= htmlspecialchars($message['direction']) ?>"
          data-message-uuid="<?= htmlspecialchars($message['uuid']) ?>">
          <?php if ($message['direction'] === 'received'): ?>
            <div class="messages-page__received-meta">
              <img
                class="messages-page__message-avatar"
                src="<?= htmlspecialchars($message['picture']) ?>"
                alt="">
              <time class="messages-page__message-time">
                <?= htmlspecialchars($message['date']) ?> <?= htmlspecialchars($message['time']) ?>
              </time>
            </div>
          <?php else: ?>
            <time class="messages-page__message-time">
              <?= htmlspecialchars($message['date']) ?> <?= htmlspecialchars($message['time']) ?>
            </time>
          <?php endif; ?>

          <p class="messages-page__bubble">
            <?= htmlspecialchars($message['content']) ?>
          </p>
        </article>
      <?php endforeach; ?>
    </div>

    <?php if ($activeConversation !== null): ?>
      <form class="messages-page__form" action="index.php?page=messages" method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="conversation_uuid" value="<?= htmlspecialchars($activeConversation['uuid']) ?>">

        <label class="messages-page__input-label" for="message-content">
          Message
        </label>
        <input
          class="messages-page__input"
          id="message-content"
          name="content"
          type="text"
          maxlength="1000"
          placeholder="Tapez votre message ici">
        <button class="messages-page__submit" type="submit">
          Envoyer
        </button>
      </form>
    <?php endif; ?>
  </section>
</section>
