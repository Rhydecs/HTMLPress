function handleContactForm(event) {
    event.preventDefault();

    const form = event.target;
    const formData = new FormData(form);

    formData.append('action', 'htmlpress_handle_contact_form');

    fetch(htmlpressAjax.ajaxurl, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Thank you for your message. We will get back to you soon!');
            form.reset();
        } else {
            alert('There was an error sending your message. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An unexpected error occurred. Please try again later.');
    });
}