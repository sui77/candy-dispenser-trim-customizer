import bpy

objText = bpy.data.objects["Text"]
objL = bpy.data.objects["L"]
objC = bpy.data.objects["C"]
objR = bpy.data.objects["R"]

# change text
objText.data.body = "###TEXT###"


objTextCopy = objText.copy();
objTextCopy.data = objText.data.copy();
bpy.context.collection.objects.link(objTextCopy)

objTextCopy.select_set(True)
bpy.context.view_layer.objects.active = objTextCopy
bpy.ops.object.convert(target="MESH")


boolDiff = objC.modifiers.new(type="BOOLEAN", name="bool 1")
boolDiff.object = objTextCopy
boolDiff.operation = 'DIFFERENCE'

objTextCopy.hide_viewport = True


width = round( objTextCopy.dimensions.x  ) + 0.5
print(width)
print( objTextCopy.dimensions.x , " d")

objL.location = (-width/2, 0, 0.05)
objR.location = (width/2, 0, 0.05)
objC.dimensions.x = width

objs = bpy.data.objects
objs.remove(objs["Text"], do_unlink=True)

bpy.ops.object.select_all(action='DESELECT')
objC.select_set(True)
objL.select_set(True)
objR.select_set(True)

# export to stl
bpy.ops.export_mesh.stl(filepath=str(('/media/files/###FILE###.stl')),   global_scale=10, use_selection=True)

#bpy.ops.export_mesh.stl(filepath=str(('c:\\tmp\\x.stl')),   global_scale=10, use_selection=True)