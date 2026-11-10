document.addEventListener('DOMContentLoaded', () => {
  const root = document;
  root.querySelectorAll('.projectpress-editor-form').forEach((form) => {
    form.addEventListener('submit', () => {
      const submit = form.querySelector('[type="submit"]');
      if (submit) submit.disabled = true;
    });
  });
});


