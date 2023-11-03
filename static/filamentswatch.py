import bpy

objSwatch = bpy.data.objects["swatch"]
objText1 = bpy.data.objects["Text1"]
objText2 = bpy.data.objects["Text2"]
objText3 = bpy.data.objects["Text3"]

# change text
objText1.data.body = "###TEXT1###"
objText2.data.body = "###TEXT2###"
objText3.data.body = "###TEXT3###"


# t1
objText1Copy = objText1.copy();
objText1Copy.data = objText1.data.copy();
bpy.context.collection.objects.link(objText1Copy)

objText1Copy.select_set(True)
bpy.context.view_layer.objects.active = objText1Copy
bpy.ops.object.convert(target="MESH")

boolDiff = objSwatch.modifiers.new(type="BOOLEAN", name="bool 1")
boolDiff.object = objText1Copy
boolDiff.operation = 'DIFFERENCE'

objText1Copy.hide_viewport = True

# t2
objText2Copy = objText2.copy();
objText2Copy.data = objText2.data.copy();
bpy.context.collection.objects.link(objText2Copy)

objText2Copy.select_set(True)
bpy.context.view_layer.objects.active = objText2Copy
bpy.ops.object.convert(target="MESH")

boolDiff = objSwatch.modifiers.new(type="BOOLEAN", name="bool 1")
boolDiff.object = objText2Copy
boolDiff.operation = 'DIFFERENCE'

objText2Copy.hide_viewport = True

#t3
objText3Copy = objText3.copy();
objText3Copy.data = objText3.data.copy();
bpy.context.collection.objects.link(objText3Copy)

objText3Copy.select_set(True)
bpy.context.view_layer.objects.active = objText3Copy
bpy.ops.object.convert(target="MESH")


boolDiff = objSwatch.modifiers.new(type="BOOLEAN", name="bool 1")
boolDiff.object = objText3Copy
boolDiff.operation = 'DIFFERENCE'

objText3Copy.hide_viewport = True



bpy.ops.object.select_all(action='DESELECT')
objSwatch.select_set(True)


# export to stl
bpy.ops.export_mesh.stl(filepath=str(('/media/files/###FILE###.stl')),   global_scale=10, use_selection=True)

#bpy.ops.export_mesh.stl(filepath=str(('c:\\tmp\\x.stl')),   global_scale=10, use_selection=True)