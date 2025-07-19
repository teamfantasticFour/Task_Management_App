console.log("kanban.js loaded!");

document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".kanban-list").forEach((list) => {
    new Sortable(list, {
      group: "kanban",
      animation: 200,
      onEnd: function (evt) {
        const taskCard = evt.item;
        const taskId = taskCard.dataset.id;
        const newStatus = evt.to.closest(".kanban-column").dataset.status;

        fetch("/kanban/update-status", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            id: taskId,
            status: newStatus,
          }),
        })
          .then((res) => res.json())
          .then((data) => {
            if (data.success) {
              console.log("Status updated");
            } else {
              Swal.fire("Gagal!", data.message || "Gagal memperbarui status", "error");
            }
          })
          .catch((err) => {
            Swal.fire("Gagal!", "Kesalahan koneksi ke server", "error");
          });
      },
    });
  });
});

fetch("/kanban/update-status", {
  method: "POST",
  headers: {
    "Content-Type": "application/json",
  },
  body: JSON.stringify({ id: taskId, status: newStatus }),
})
  .then((res) => res.json())
  .then((data) => {
    console.log("Response dari server:", data); // ⬅ Tambahkan ini
    if (!data.success) {
      Swal.fire("Gagal!", data.message || "Gagal memperbarui status", "error");
    }
  })
  .catch((err) => {
    console.error("Fetch error:", err); // ⬅ Tambahkan ini juga
    Swal.fire("Gagal!", "Kesalahan jaringan saat update status", "error");
  });
