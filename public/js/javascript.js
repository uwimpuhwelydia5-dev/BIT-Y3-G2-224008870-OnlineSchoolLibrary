



```javascript
// ======================================
// HOMEPAGE SEARCH
// ======================================

function searchBooks() {
    const searchInput = document.getElementById("searchInput");

    if (!searchInput) {
        return;
    }

    const query = searchInput.value.trim();

    if (query === "") {
        alert("Please enter a book title, author, or category.");
        searchInput.focus();
        return;
    }

    window.location.href =
        "/Online-library/public/home-search.php?q=" +
        encodeURIComponent(query);
}


// ======================================
// LIVE SEARCH ON MANAGE E-BOOKS PAGE
// ======================================

document.addEventListener("DOMContentLoaded", function () {

    const searchInput = document.getElementById("bookSearch");
    const resultsContainer = document.getElementById("searchResults");

    if (!searchInput || !resultsContainer) {
        return;
    }

    let searchTimer;

    searchInput.addEventListener("input", function () {

        clearTimeout(searchTimer);

        const query = searchInput.value.trim();

        if (query === "") {
            resultsContainer.innerHTML = "";
            return;
        }

        searchTimer = setTimeout(function () {

            fetch(
                "/Online-library/public/search-books.php?q=" +
                encodeURIComponent(query)
            )
                .then(function (response) {

                    if (!response.ok) {
                        throw new Error("Search request failed.");
                    }

                    return response.json();
                })

                .then(function (books) {

                    resultsContainer.innerHTML = "";

                    if (!Array.isArray(books) || books.length === 0) {
                        resultsContainer.innerHTML =
                            "<p>No books found.</p>";
                        return;
                    }

                    const table = document.createElement("table");

                    table.innerHTML = `
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Category</th>
                                <th>Copies</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    `;

                    const tbody = table.querySelector("tbody");

                    books.forEach(function (book) {

                        const row = document.createElement("tr");

                        [
                            book.ebook_id,
                            book.title,
                            book.author,
                            book.category,
                            book.copies
                        ].forEach(function (value) {

                            const cell = document.createElement("td");
                            cell.textContent = value ?? "";
                            row.appendChild(cell);

                        });

                        tbody.appendChild(row);

                    });

                    resultsContainer.appendChild(table);

                })

                .catch(function (error) {

                    console.error("Search error:", error);

                    resultsContainer.innerHTML =
                        "<p>Unable to search books. Please try again.</p>";

                });

        }, 300);

    });

});
```