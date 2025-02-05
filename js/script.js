// Add any JavaScript functionality here if needed
```javascript```
// Example: Simple form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const title = document.querySelector('input[name="title"]').value;
    const content = document.querySelector('textarea[name="content"]').value;

    if (title.trim() === '' || content.trim() === '') {
        e.preventDefault();
        alert('Please fill in all fields.');
    }
});