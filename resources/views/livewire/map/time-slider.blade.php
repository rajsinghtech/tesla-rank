<div class="relative w-full h-[40px] border border-slate-400 rounded" id="slider-container">
    {{-- Tick Marks --}}
    <div class="absolute top-[50%] w-full h-full pointer-events-none" id="ticks-container"></div>

    {{-- Slider Box --}}
    <div class="absolute top-[-2px] bottom-[-2px] left-0 right-0 border border-4 border-orange-500 rounded-lg"
        id="slider-box"></div>

    {{-- Slider Handles --}}
    <div class="absolute top-1/2 left-0 w-[10px] h-[10px] bg-red-600 rounded-full -translate-y-1/2" id="slider-left">
    </div>
    <div class="absolute top-1/2 right-0 w-[10px] h-[10px] bg-red-600 rounded-full -translate-y-1/2" id="slider-right">
    </div>

    @once
        <style>
            .tick {
                position: absolute;
                top: 50%;
                width: 2px;
                height: 10px;
                background-color: gray;
                transform: translateY(-50%);
                user-select: none;
                -webkit-user-drag: none;
            }

            .tick-label {
                position: absolute;
                top: 60%;
                transform: translateX(-50%);
                font-size: 10px;
                color: #666;
            }
        </style>
    @endonce
</div>

@script
    <script>
        const ticksContainer = document.getElementById('ticks-container');
        const totalTicks = 96; // Total number of ticks
        const tickPercentage = 100 / totalTicks; // Percentage width for each tick

        // Generate ticks
        for (let i = 0; i <= totalTicks; i++) {
            const tick = document.createElement('div');
            tick.classList.add('tick');
            tick.style.left = `${i * tickPercentage}%`;

            // Add label for every 4th tick (1-hour interval)
            if (i % 4 === 0) {
                const label = document.createElement('div');
                label.classList.add('tick-label');
                label.innerText = formatTime(i);
                tick.appendChild(label);
            }

            ticksContainer.appendChild(tick);
        }

        // Format time for the labels
        function formatTime(tickIndex) {
            const totalMinutes = tickIndex * 15;
            const hours = Math.floor(totalMinutes / 60);
            const minutes = totalMinutes % 60;
            const period = hours < 12 ? 'AM' : 'PM';

            const formattedHours = hours % 12 === 0 ? 12 : hours % 12;
            const formattedMinutes = minutes < 10 ? `0${minutes}` : minutes;

            return `${formattedHours}:${formattedMinutes} ${period}`;
        }

        // SLIDER

        const sliderContainer = document.getElementById('slider-container');
        const sliderBox = document.getElementById('slider-box');
        const sliderLeft = document.getElementById('slider-left');
        const sliderRight = document.getElementById('slider-right');

        // Initialize positions as percentages
        sliderLeft.style.left = '0%';
        sliderRight.style.right = '0%';

        sliderLeft.addEventListener('mousedown', (e) => {
            e.preventDefault();
            document.addEventListener('mousemove', onMoveLeftSlider);
            document.addEventListener('mouseup', onMouseUp);
        });

        sliderRight.addEventListener('mousedown', (e) => {
            e.preventDefault();
            document.addEventListener('mousemove', onMoveRightSlider);
            document.addEventListener('mouseup', onMouseUp);
        });

        function onMoveLeftSlider(e) {
            const sliderContainerRect = sliderContainer.getBoundingClientRect();
            const sliderWidth = sliderContainerRect.width;

            // Calculate the percentage of the slider
            const rawLeftPercentage = ((e.clientX - sliderContainerRect.left) / sliderWidth) * 100;
            const snappedLeftPercentage = snapToTick(rawLeftPercentage);
            const rightPercentage = parseFloat(sliderRight.style.right);

            if (
                snappedLeftPercentage >= 0 &&
                snappedLeftPercentage <= (100 - rightPercentage - tickPercentage)
            ) {
                sliderLeft.style.left = `${snappedLeftPercentage}%`;
                sliderBox.style.left = sliderLeft.style.left;
            }
        }

        function onMoveRightSlider(e) {
            const sliderContainerRect = sliderContainer.getBoundingClientRect();
            const sliderWidth = sliderContainerRect.width;

            // Calculate the percentage of the slider
            const rawRightPercentage = ((sliderContainerRect.right - e.clientX) / sliderWidth) * 100;
            const snappedRightPercentage = snapToTick(rawRightPercentage);
            const leftPercentage = parseFloat(sliderLeft.style.left);

            if (
                snappedRightPercentage >= 0 &&
                snappedRightPercentage <= (100 - leftPercentage - tickPercentage)
            ) {
                sliderRight.style.right = `${snappedRightPercentage}%`;
                sliderBox.style.right = sliderRight.style.right;
            }
        }

        function snapToTick(rawPercentage) {
            // Find the nearest tick percentage
            const tickIndex = Math.round(rawPercentage / tickPercentage);
            return tickIndex * tickPercentage;
        }

        function onMouseUp() {
            document.removeEventListener('mousemove', onMoveLeftSlider);
            document.removeEventListener('mousemove', onMoveRightSlider);
            document.removeEventListener('mouseup', onMouseUp);
        }
    </script>
@endscript
