// STLViewer.js
// Written by Christian Lölkes
// Based on examples from threejs.org

var loader;
var controls;

var camera;
var camera_fov;

var mesh_object;
var mesh_floor;

var scene;
var renderer;
var renderer_antialias = true;
var material_object;

var dimensions = new THREE.Vector4();

var loaded;
var geometry_object;
var material_floor;
var geometry_floor;
var texture_floor;

var floor_scale_x = 100;
var floor_scale_y = 100;
var floor_scale_z = 1;
var floor_scale = new THREE.Vector3( floor_scale_x, floor_scale_y, floor_scale_z );
var floor_repeat_x = 10;
var floor_repeat_y = 10;
var floor_repeat = new THREE.Vector2( floor_repeat_x, floor_repeat_y);

//var fog_color = 0xd9dee5;
//var fog_near = 1;
//var fog_far = 10000;
var fog;

//var ambient_light_color = 0x202020;
var ambient_light;

var point_light;
//var point_light_color = 0xffffff;
//var point_light_intensity = 0.7;

var directional_light;
//var directional_light_color = 0xffffff;
//var directional_light_intensity = 0.7;

function viewTop() {
    var size;
    var size_factor = 2.2;
    if( dimensions.y > dimensions.x ) size = dimensions.y;
    else size = dimensions.x;
    camera.position.set( 0, 0, size * size_factor );
    camera.up.set( 0, 1, 0);
}

function viewSide( side ) {
    var size;
    var size_factor = 2.2;
    var window_factor = SCREEN_HEIGHT/SCREEN_WIDTH;
    var factor = new THREE.Vector4( 0, 0, THREE.Math.clamp( window_factor, 0, 1 ), THREE.Math.clamp( 1 / window_factor, 0, 1 ) );

    if( dimensions.z > dimensions.x ) size = dimensions.z;
    else size = dimensions.x;

    if( side == 'front' )      { factor.setComponent(1, -1); }
    else if( side == 'rear' )  { factor.setComponent(1, 1); }
    else if( side == 'left' )  { factor.setComponent(0, -1); }
    else if( side == 'right' ) { factor.setComponent(0, 1); }
    else { factor.setComponent(1, -1); } // Default to front

    camera.position.set( factor.x * ( size * size_factor * factor.z + dimensions.x ), factor.y * ( size * size_factor * factor.w - dimensions.y) ,0 );
    camera.up.set( 0, 0, 1 );
}

// IMPORTANT

function $( id ) {
	return document.getElementById( id );
}

function noWebGL() { 	// Runs if no WebGL is found
	$( 'progress' ).style.display = 'none';
	$( 'canvas' ).style.display = 'none';
	$( 'webGLError' ).style.display = 'block';
	$( 'quality_notes' ).style.display = 'none';
} // End of noWebGL()
	
function init( inputfiletype ) {
    $( 'progress' ).style.display = 'block';
    $( 'canvas' ).style.display = 'block';
    $( 'webGLError' ).style.display = 'none';

	camera      = new THREE.PerspectiveCamera( camera_fov, SCREEN_WIDTH / SCREEN_HEIGHT, 1, 25000 );
    controls    = new THREE.TrackballControls( camera, container );
    scene       = new THREE.Scene();
    renderer    = new THREE.WebGLRenderer( { antialias: renderer_antialias } );
    fog         = new THREE.Fog( fog_color, fog_near, fog_far );
    ambient_light       = new THREE.AmbientLight( ambient_light_color );
    directional_light   = new THREE.DirectionalLight( directional_light_color, directional_light_intensity );
    point_light         = new THREE.PointLight( point_light_color, point_light_intensity);
    material_object     = new THREE.MeshLambertMaterial( { color:0xffffff, shading: THREE.FlatShading } );
    //material_object = new THREE.MeshPhongMaterial( { ambient: 0x555555, color: 0xffffff, specular: 0x111111, shininess: 200 } );

    texture_floor   = THREE.ImageUtils.loadTexture( floor );
    material_floor  = new THREE.MeshBasicMaterial( { map: texture_floor } );
    geometry_floor  = new THREE.PlaneGeometry( 100, 100 );
    mesh_floor      = new THREE.Mesh( geometry_floor, material_floor );

    loader = new THREE.STLLoader();
    //if(inputfiletype == 'STL') loader = new THREE.STLLoader();
    //if(inputfiletype == 'OBJ') loader = new THREE.OBJLoader();

    controls.rotateSpeed = 1.0;
    controls.zoomSpeed = 1.2;
    controls.panSpeed = 0.8;

    controls.noZoom = false;
    controls.noPan = true;
    controls.noRotate = false;

    controls.staticMoving = false;
    controls.dynamicDampingFactor = 0.3;

    controls.keys = [ 65, 83, 68 ];
    controls.addEventListener( 'change', render );

    scene.fog = fog;
				
	scene.add( ambient_light );
    directional_light.position.normalize();
    scene.add( directional_light );
    scene.add( point_light );
    camera.lookAt( 0, 0, 0 );

    loader.load( file, function ( geometry ) {
        geometry_object = geometry;
		mesh_object = new THREE.Mesh( geometry, material_object );
        mesh_object.castShadow = mesh_object.receiveShadow = true;

        geometry.computeBoundingBox();
        geometry.computeBoundingSphere();

        dimensions.subVectors( geometry.boundingBox.max, geometry.boundingBox.min );
        dimensions.setW( geometry.boundingSphere.radius );

        setRotationOffset();

        camera.position.set( 0, 0, dimensions.w * 2.2 );
		mesh_object.rotation.copy( object_rotation_offset );
        setLights();
        setFloor();
        scene.add( mesh_object );

	} );

	// Floor
    texture_floor.wrapS = texture_floor.wrapT = THREE.RepeatWrapping;
    texture_floor.repeat.copy( floor_repeat );

	mesh_floor.scale.copy( floor_scale );
	mesh_floor.receiveShadow = true;
	scene.add( mesh_floor );

	// RENDERER
	renderer.setSize( SCREEN_WIDTH, SCREEN_HEIGHT );
	renderer.setClearColor( scene.fog.color, 1 );
	renderer.autoClear = false;

	renderer.setSize(container.offsetWidth, container.offsetHeight);
	renderer.domElement.style.position = 'relative';
	container.appendChild( renderer.domElement );

} // End of init()

function animate() {
    if ( geometry_object && !loaded ) {
        loaded = true;
        mesh_floor.position.z = - dimensions.z / 2;
        viewSide('front');
        $( 'progress' ).style.display = 'none';
    }
    requestAnimationFrame( animate );
    controls.update();
	render();

} // End of animate()

function render() {
	camera.lookAt( scene.position );
	renderer.clear();
	renderer.render( scene, camera );
} // End of render()

// Le awesome stuff

if ( ! Detector.webgl ) noWebGL();
else {
    init('STL');
    animate();
}