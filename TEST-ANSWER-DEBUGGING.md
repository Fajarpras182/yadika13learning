<!-- Test berapa jawaban yang disimpan di form saat submit -->
<script>
// Debugging: Log semua jawaban yang akan dikirim saat form submit
document.getElementById('examForm').addEventListener('submit', function(e) {
    const formData = new FormData(this);
    const answers = [];
    for (let [key, value] of formData.entries()) {
        if (key.startsWith('answers')) {
            answers.push(`${key}: ${value}`);
        }
    }
    console.log('=== JAWABAN YANG AKAN DIKIRIM ===');
    console.log(answers);
    console.log('Total jawaban:', answers.length);
    console.log('=== Object Answers di Memory ===');
    console.log(window.answers);
});
</script>
