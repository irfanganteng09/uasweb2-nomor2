<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>To-Do List</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
<div class="max-w-md w-full bg-white rounded shadow p-6 space-y-4">
<h1 class="text-2xl font-semibold">To-Do List</h1>
<div class="space-y-2">
<input id="new-task" type="text" placeholder="Tambah tugas baru" class="border w-full px-3 py-2 rounded">
<button id="add-btn" class="bg-blue-600 text-white px-3 py-2 rounded">Tambah</button>
</div>
<ul id="task-list" class="space-y-2 pt-4"></ul>
</div>
<script>
async function loadTasks() {
  const res = await fetch('api.php/todos'); 
  const data = await res.json();
  const list = document.getElementById('task-list');
  list.innerHTML = '';
  data.forEach(item => {
    const li = document.createElement('li');
    li.className = 'flex justify-between items-center bg-gray-50 p-2 rounded';
    const check = document.createElement('input');
    check.type = 'checkbox';
    check.checked = item.is_done == 1;
    check.addEventListener('change', async () => {
      await fetch('api.php/todos/' + item.id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title: item.title, is_done: check.checked ? 1 : 0 })
      });
      loadTasks();
    });
    const txt = document.createElement('input');
    txt.type = 'text';
    txt.value = item.title;
    txt.className = 'border border-transparent focus:border-gray-300 rounded px-1 mx-2 flex-grow';
    txt.addEventListener('blur', async () => {
      await fetch('api.php/todos/' + item.id, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title: txt.value, is_done: check.checked ? 1 : 0 })
      });
      loadTasks();
    });
    const btnDel = document.createElement('button');
    btnDel.textContent = 'Hapus';
    btnDel.className = 'bg-red-600 text-white px-3 py-1 rounded';
    btnDel.addEventListener('click', async () => {
      await fetch('api.php/todos/' + item.id, {
        method: 'DELETE'
      });
      loadTasks();
    });
    li.appendChild(check);
    li.appendChild(txt);
    li.appendChild(btnDel);
    list.appendChild(li);
  });
}
document.getElementById('add-btn').addEventListener('click', async () => {
  const input = document.getElementById('new-task');
  if (input.value.trim() !== '') {
    await fetch('api.php/todos', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ title: input.value.trim() })
    });
    input.value = '';
    loadTasks();
  }
});
window.addEventListener('DOMContentLoaded', loadTasks);
</script>
</body>
</html>
