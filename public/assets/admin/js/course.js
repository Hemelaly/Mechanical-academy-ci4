            // Accordion functionality - only one open at a time
        document.addEventListener('DOMContentLoaded', function() {
            const accordion = document.getElementById('courseAccordion');
            
            // Use Bootstrap's built-in event system for better control
            accordion.addEventListener('show.bs.collapse', function(e) {
                // Close all other open accordion items
                const openItems = accordion.querySelectorAll('.accordion-collapse.show');
                
                openItems.forEach(item => {
                    if (item !== e.target) {
                        const bsCollapse = bootstrap.Collapse.getInstance(item);
                        if (bsCollapse) {
                            bsCollapse.hide();
                        }
                    }
                });
            });
        });


// Lógica de seleção de aula (aplica highlight e troca o player de mock)
const lessons = document.querySelectorAll('.lesson-item');
const player = document.querySelector('#player');

function setActive(lesson) {
    lessons.forEach((lesson) => {
        lesson.classList.remove('active');
    });
    lesson.classList.add('active');
    // lessons.forEach(el => el.classList.remove('bg-blue-50', 'ring-1', 'ring-blue-200'));
    // lesson.classList.add('bg-blue-50', 'ring-1', 'ring-blue-200');
    const videoId = extrairVideoId(lesson.dataset.video);

    if (videoId) {
        player.src = `https://youtube.com/embed/${videoId}`;
        
        document.querySelector('#title').innerText = lesson.dataset.title;
        document.querySelector('#views').innerText = lesson.dataset.views;
        document.querySelector('#time').innerText = lesson.dataset.time;
        document.querySelector('#module').innerText = lesson.dataset.module;
        document.querySelector('#description').innerHTML = lesson.dataset.description;
    } else {
        alert('URL invalida!');
    }
}

function extrairVideoId(url) {
    try {
        const u = new URL(url);
        if (u.hostname.includes('youtube.com')) {
            return u.searchParams.get('v'); // https://www.youtube.com/watch?v=ID
        } else if (u.hostname === 'youtu.be') {
            return u.pathname.substring(1); // https://youtu.be/ID
        }
    } catch (e) {
        return null;
    }
}

lessons.forEach(lesson => {
    lesson.addEventListener('click', () => setActive(lesson));
});