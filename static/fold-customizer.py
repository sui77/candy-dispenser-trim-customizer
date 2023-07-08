import bpy

"""col = bpy.data.collections["Text"]"""

text="[###TEXTUPPER###]"
"""bpy.data.objects["C-A.004"].location = (4, 0, 0)"""

newLetters = []
x=0

for i, c in enumerate(text):
    print (i, c, x)
    currentLetter = "C-" + c;
    if not "C-"+c in bpy.context.scene.objects.keys():
        currentLetter = "C-_"

    objLetter = bpy.data.objects[currentLetter]
    objLetterCopy = objLetter.copy()

    bpy.context.collection.objects.link(objLetterCopy)
    newLetters.append(objLetterCopy)

    objLetterCopy.location = (x+objLetter.dimensions.x/2,0,0)
    x +=  objLetter.dimensions.x;

    objLetterCopy.select_set(True)

    bpy.ops.export_mesh.stl(filepath=str(('/media/files/###FILE###.stl')),   global_scale=10, use_selection=True)