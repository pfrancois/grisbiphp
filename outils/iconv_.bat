@for %%d in (*.smarty) do call iconv_2.bat %%d
del "%1"
ren "%1_" "%1"