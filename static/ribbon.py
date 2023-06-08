import bpy

objText = bpy.data.objects["Text"]
objRibbon =  bpy.data.objects["Ribbon"]

# change text and convert to mesh
objText.data.body = "###TEXT###"
objText.select_set(True)
bpy.context.view_layer.objects.active = objText
bpy.ops.object.convert(target="MESH")

# change to edit mode
bpy.ops.object.mode_set( mode = 'EDIT' )
bpy.ops.mesh.select_all( action='SELECT' )

# extrude text
bpy.ops.mesh.extrude_region_move(
        TRANSFORM_OT_translate={
            "value":(0, 0, 0.1),
            "orient_type":'NORMAL',
            "orient_matrix":((0, -1, 0), (1, 0, -0), (0, 0, 1)),
            "orient_matrix_type":'NORMAL',
            "constraint_axis":(False, False, True),
        }
    )

# union ribbon + text
bool = objRibbon.modifiers.new(type="BOOLEAN", name="bool 1")
bool.object = objText
bool.operation = 'UNION'



# select ribbon
bpy.ops.object.mode_set( mode = 'OBJECT' )
bpy.ops.object.select_all(action='DESELECT')
objRibbon.select_set(True)
bpy.context.view_layer.objects.active = objRibbon

# export to stl
bpy.ops.export_mesh.stl(filepath=str(('/media/files/###FILE###.stl')),   global_scale=10, use_selection=True)
#bpy.ops.export_mesh.stl(filepath=str(('x.stl')),   global_scale=10, use_selection=True)