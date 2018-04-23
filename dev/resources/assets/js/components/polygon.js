$( document ).ready(function() {
    
    var MESH = {
        segments: 12,
        slices: 5,
        xRange: 0.3,
        yRange: 0.3,
        zRange: 0.5,
        ambient: '#000021',
        diffuse: '#FFFFFF',
        speed: 0.0002,
        light_z: 150,
        light_source_dark: '#000705',
        light_source_light: '#239192',
    };
    var container = document.getElementById('geoBackdrop');
    var renderer = new FSS.WebGLRenderer();
    var scene = new FSS.Scene();
    var light = new FSS.Light(MESH.light_source_dark, MESH.light_source_light);
    var geometry, material, mesh;
    var now, start = Date.now();

    function initialise() {
        if (container !== null) {
            createMesh();
            resize();
            animate();

            scene.add(light);
            container.appendChild(renderer.element);
            window.addEventListener('resize', resize);
        }
    }

    function createMesh() {
        scene.remove(mesh);
        renderer.clear();
        geometry = new FSS.Plane(container.offsetWidth + (container.offsetWidth/MESH.segments)*4, container.offsetHeight, MESH.segments, MESH.slices);
        renderer.element.style.marginLeft = (-1*(container.offsetWidth/MESH.segments))+'px';
        material = new FSS.Material(MESH.ambient, MESH.diffuse);
        mesh = new FSS.Mesh(geometry, material);
        scene.add(mesh);

        // Augment vertices for animation
        var v, vertex;
        for (v = geometry.vertices.length - 1; v >= 0; v--) {
            vertex = geometry.vertices[v];
            vertex.anchor = FSS.Vector3.clone(vertex.position);
            vertex.step = FSS.Vector3.create(
                Math.randomInRange(0.2, 1.0),
                Math.randomInRange(0.2, 1.0),
                Math.randomInRange(0.2, 1.0)
            );
            vertex.time = Math.randomInRange(0, Math.PIM2);
        }
    }

    function resize() {
        renderer.setSize(container.offsetWidth + (container.offsetWidth/MESH.segments)*4, container.offsetHeight);
    }

    function animate() {
        now = Date.now() - start;
        light.setPosition(300 * Math.sin(now * 0.001), 200 * Math.cos(now * 0.0005), MESH.light_z);
        renderer.render(scene);

        for (v = geometry.vertices.length - 1; v >= 0; v--) {
            vertex = geometry.vertices[v];
            var offset = 0.5;
            ox = Math.sin(vertex.time + vertex.step[0] * now * MESH.speed);
            oy = Math.cos(vertex.time + vertex.step[1] * now * MESH.speed);
            oz = Math.sin(vertex.time + vertex.step[2] * now * MESH.speed);
            FSS.Vector3.set(vertex.position,
                MESH.xRange * geometry.segmentWidth * ox,
                MESH.yRange * geometry.sliceHeight * oy,
                MESH.zRange * offset * oz - offset);
            FSS.Vector3.add(vertex.position, vertex.anchor);
        }

        // Set the Geometry to dirty
        geometry.dirty = true;

        requestAnimationFrame(animate);
    }

    initialise();
});

