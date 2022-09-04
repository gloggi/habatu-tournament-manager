f = open('D:/Dokumente/HaBaTu 2022/habatu-backend/test/rest/sections.rest', "r")

text = f.read();
f.close()
tests = ["Option"]

for test in tests:
    f = open("%s%s.rest"%('D:/Dokumente/HaBaTu 2022/habatu-backend/test/rest/',test), "w")
    temptext = text
    temptext= temptext.replace("section",test.lower())
    temptext=temptext.replace("Section",test)
    f.write(temptext)
    f.close()
    