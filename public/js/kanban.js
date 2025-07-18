console.log("kanban.js loaded!");
document.addEventListener("DOMContentLoaded", () => {
  document.querySelectorAll(".kanban-list").forEach((list) => {
    new Sortable(list, {
      group: "kanban",
      animation: 200,
      ghostClass: "ghost",
      onEnd: function (evt) {
        const taskCard = evt.item;
        const taskId = taskCard.dataset.id;
        const newStatus = evt.to.closest(".kanban-column").dataset.status;

        // AJAX update
        fetch("/kanban/update-status", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ id: taskId, status: newStatus }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              Swal.fire({
                icon: "success",
                title: "Berhasil!",
                text: "Status tugas berhasil diperbarui.",
                timer: 1500,
                showConfirmButton: false,
              });
            } else {
              Swal.fire({
                icon: "error",
                title: "Gagal!",
                text: data.message || "Terjadi kesalahan saat memperbarui status.",
              });
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            Swal.fire({
              icon: "error",
              title: "Gagal!",
              text: "Terjadi kesalahan koneksi ke server.",
            });
          });
      },
    });
  });
});
