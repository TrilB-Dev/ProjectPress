document.addEventListener('DOMContentLoaded', () => {
	const root = document;
	root.querySelectorAll('[data-projectpress-count]').forEach((element) => {
		element.classList.add('projectpress-count-ready');
	});
});



