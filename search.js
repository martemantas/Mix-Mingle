document.addEventListener("DOMContentLoaded", function() {
    function fetchIngredients(callback) {
        var xhr = new XMLHttpRequest();
        var url = 'fetchIngredients.php';
        xhr.open('GET', url, true);
    
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var ingredients = JSON.parse(xhr.responseText);
                    callback(ingredients); 
                } else {
                    console.error('Failed to fetch recipes:', xhr.status);
                }
            }
        };
    
        xhr.send();
    }
    

    function createDropdown(ingredients) {
        var dropdown = document.createElement('select');
        dropdown.classList.add('ingredient-select');
    
        ingredients.forEach(function(ingredient) {
            var option = document.createElement('option');
            option.value = ingredient;
            option.textContent = ingredient;
            dropdown.appendChild(option);
        });
    
        return dropdown;
    }
    
    function handleAddIngredient(event) {
        fetchIngredients(function(ingredients) {
            var newDropdown = createDropdown(ingredients);
            
            var removeButton = document.createElement('button');
            removeButton.textContent = 'Remove';
            removeButton.classList.add('remove-ingredient');
    
            removeButton.addEventListener('click', function() {
                newDropdown.remove();
                removeButton.remove();
            });
    
            var parentDiv = event.target.parentElement;
            parentDiv.insertBefore(newDropdown, parentDiv.lastElementChild);
            parentDiv.insertBefore(removeButton, parentDiv.lastElementChild);
        });
    }

    var addButton = document.querySelectorAll('.add-ingredient');
    addButton.forEach(function(button) {
        button.addEventListener('click', handleAddIngredient);
    });
});