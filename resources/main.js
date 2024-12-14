document.addEventListener('DOMContentLoaded', function () {
    const chatForm = document.getElementById('chat-form');

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(chatForm);

        fetch('send_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            chatForm.reset();
            loadMessages();
        });
    });

    function loadMessages() {
        fetch('load_messages.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('chat-box').innerHTML = data;
            });
    }

    function loadUserStatus() {
        fetch('load_user_status.php')
            .then(response => response.text())
            .then(data => {
                document.getElementById('user-list').innerHTML = data;
            });
    }

    setInterval(loadMessages, 1000);
    setInterval(loadUserStatus, 10000); 
});