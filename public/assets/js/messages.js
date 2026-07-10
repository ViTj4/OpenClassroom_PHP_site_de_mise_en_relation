document.addEventListener('DOMContentLoaded', () => {
  const thread       = document.querySelector('[data-conversation-uuid][data-poll-url][data-send-url]');
  const messagesList = document.querySelector('[data-messages-list]');
  const form         = document.querySelector('.messages-page__form');
  const input        = document.querySelector('.messages-page__input');

  if (!thread || !messagesList) {
    return;
  }

  let isPolling = false;

    // On considère l'utilisateur "en bas" du fil si le scroll est proche de la fin.
    // Cela évite de le ramener en bas pendant qu'il relit d'anciens messages.
  const isMessagesScrolledNearBottom = () => (
    messagesList.scrollHeight - messagesList.scrollTop - messagesList.clientHeight < 80
  );

  const scrollMessagesToBottom = () => {
    messagesList.scrollTop = messagesList.scrollHeight;
  };

  const readJsonResponse = async (response, fallbackMessage) => {
    const text = await response.text();

    try {
      const data = text ? JSON.parse(text) : {};

      if (!response.ok) {
        throw new Error(data.detail ? `${data.error || fallbackMessage} ${data.detail}` : data.error || fallbackMessage);
      }

      return data;
    } catch (error) {
      if (error instanceof SyntaxError) {
          // Utile en debug : si PHP renvoie une page d'erreur HTML, on affiche un extrait lisible.
        throw new Error(`${fallbackMessage} Réponse serveur inattendue : ${text.slice(0, 300)}`);
      }

      throw error;
    }
  };

  const buildMessageElement = (message) => {
    const article                     = document.createElement('article');
          article.className           = `messages-page__message messages-page__message--${message.direction}`;
          article.dataset.messageUuid = message.uuid;

    if (message.direction === 'received') {
      const meta           = document.createElement('div');
            meta.className = 'messages-page__received-meta';

      const avatar           = document.createElement('img');
            avatar.className = 'messages-page__message-avatar';
            avatar.src       = message.picture;
            avatar.alt       = '';

      const time             = document.createElement('time');
            time.className   = 'messages-page__message-time';
            time.textContent = `${message.date} ${message.time}`;

      meta.append(avatar, time);
      article.append(meta);
    } else {
      const time             = document.createElement('time');
            time.className   = 'messages-page__message-time';
            time.textContent = `${message.date} ${message.time}`;
      article.append(time);
    }

    const bubble             = document.createElement('p');
          bubble.className   = 'messages-page__bubble';
          bubble.textContent = message.content;
    article.append(bubble);

    return article;
  };

  const renderMessages = (messages, forceScroll = false) => {
    const shouldScrollToBottom = forceScroll || isMessagesScrolledNearBottom();
    const messageElements      = messages.map(buildMessageElement);
    messagesList.replaceChildren(...messageElements);

    if (shouldScrollToBottom) {
      scrollMessagesToBottom();
    }
  };

  const updateActiveConversationPreview = (messages) => {
    const lastMessage   = messages.at(-1);
    const activePreview = document.querySelector('.messages-page__conversation--active .messages-page__conversation-preview');

    if (!lastMessage || !activePreview) {
      return;
    }

    activePreview.textContent = lastMessage.content.length > 34
      ? `${lastMessage.content.slice(0, 31)}...`
      :  lastMessage.content;
  };

  const pollMessages = async (forceScroll = false) => {
    if (isPolling || document.hidden) {
      return;
    }

      // Le polling évite de recharger la page pour recevoir les nouveaux messages.
      // isPolling empêche deux requêtes simultanées si le serveur répond lentement.
    isPolling = true;

    try {
      const response = await fetch(thread.dataset.pollUrl, {
        headers: {
          Accept: 'application/json',
        },
      });
      const data = await readJsonResponse(response, 'Impossible de rafraichir les messages.');

      if (Array.isArray(data.messages)) {
        renderMessages(data.messages, forceScroll);
        updateActiveConversationPreview(data.messages);
      }
    } catch (error) {
        // La messagerie retentera au prochain cycle de polling.
    } finally {
      isPolling = false;
    }
  };

  const handleSubmit = async (event) => {
    event.preventDefault();

    const message = input?.value.trim() || '';

    if (!message) {
      return;
    }

    const body = new URLSearchParams({
      csrf_token       : form.querySelector('[name="csrf_token"]')?.value || '',
      conversation_uuid: thread.dataset.conversationUuid,
      content          : message,
    });

    const response = await fetch(thread.dataset.sendUrl, {
      method : 'POST',
      headers: {
        Accept            : 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body,
    });

    await readJsonResponse(response, 'Impossible d\'envoyer le message.');
    input.value = '';
      // Après un envoi, on force le scroll en bas pour voir immédiatement son message.
    await pollMessages(true);
  };

  form?.addEventListener('submit', handleSubmit);
    // Au chargement d'un fil existant, on place l'utilisateur sur les derniers messages.
  window.setTimeout(scrollMessagesToBottom, 0);
  window.setInterval(pollMessages, 5000);
});
